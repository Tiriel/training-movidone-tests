<?php

namespace App\Twig\Components;

use App\Entity\Event;
use App\Entity\Project;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveListener;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\ValidatableComponentTrait;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

#[AsLiveComponent]
final class NewEventForm
{
    use FormWithOrganizationsTrait;
    use FormWithTagsTrait;
    use DefaultActionTrait;
    use ValidatableComponentTrait;

    #[LiveProp(writable: true)]
    #[Assert\NotBlank()]
    #[Assert\Length(min: 10)]
    public string $name = '';

    #[LiveProp(writable: true)]
    #[Assert\NotBlank()]
    #[Assert\Length(min: 30)]
    public string $description = '';

    #[LiveProp(writable: true)]
    public bool $isAccessible = false;

    #[LiveProp(writable: true)]
    #[Assert\Length(min: 20)]
    public ?string $prerequisites = null;

    #[LiveProp(writable: true, format: 'Y-m-d')]
    #[Assert\NotBlank]
    #[Assert\GreaterThanOrEqual('today')]
    public ?\DateTime $startAt = null;

    #[LiveProp(writable: true, format: 'Y-m-d')]
    #[Assert\NotBlank]
    #[Assert\GreaterThanOrEqual(propertyPath: 'startAt')]
    public ?\DateTime $endAt = null;

    #[LiveProp(writable: true)]
    #[Assert\Valid]
    public ?Project $project = null;

    public function __construct(
        private readonly EntityManagerInterface $manager,
        private readonly UrlGeneratorInterface $urlGenerator,
    ) {
    }

    #[ExposeInTemplate]
    public function projects(): array
    {
        if ([] === $this->organizations) {
            return $this->manager->getRepository(Project::class)->findAll();
        }

        return $this->manager->getRepository(Project::class)->findForOrganizations($this->organizations);
    }

    #[LiveListener('project:created')]
    public function onProjectCreated(#[LiveArg] Project $project): void
    {
        $this->project = $project;
    }

    public function isProjectSelected(Project $project): bool
    {
        return $this->project && $project === $this->project;
    }

    #[LiveAction]
    public function saveEvent(EntityManagerInterface $manager): Response
    {
        $this->validate();

        $event = (new Event())
            ->setName($this->name)
            ->setDescription($this->description)
            ->setAccessible($this->isAccessible)
            ->setStartAt(\DateTimeImmutable::createFromMutable($this->startAt))
            ->setEndAt(\DateTimeImmutable::createFromMutable($this->endAt))
            ->setProject($this->project)
        ;

        if (null !== $this->prerequisites) {
            $event->setPrerequisites($this->prerequisites);
        }

        foreach ($this->organizations as $organization) {
            $event->addOrganization($organization);
        }

        foreach ($this->tags as $tag) {
            $event->addTag($tag);
        }

        $manager->persist($event);
        $manager->flush();

        return new RedirectResponse($this->urlGenerator->generate('app_event_show', ['id' => $event->getId()]));
    }
}
