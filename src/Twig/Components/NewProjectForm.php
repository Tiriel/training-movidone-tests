<?php

namespace App\Twig\Components;

use App\Entity\Project;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Clock\ClockAwareTrait;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\ValidatableComponentTrait;

#[AsLiveComponent]
final class NewProjectForm
{
    use FormWithOrganizationsTrait;
    use FormWithTagsTrait;
    use ClockAwareTrait;
    use ComponentToolsTrait;
    use DefaultActionTrait;
    use ValidatableComponentTrait;

    #[LiveProp(writable: true)]
    #[NotBlank]
    public string $name = '';

    #[LiveProp(writable: true)]
    #[NotBlank]
    #[Length(min: 30)]
    public string $summary = '';

    #[LiveProp]
    public bool $redirect = false;

    public function __construct(
        private readonly EntityManagerInterface $manager,
        private readonly UrlGeneratorInterface $urlGenerator,
    ) {
    }

    #[LiveAction]
    public function saveProject(#[CurrentUser] User $user, EntityManagerInterface $manager): ?RedirectResponse
    {
        $this->validate();

        $project = (new Project())
            ->setName($this->name)
            ->setSummary($this->summary)
            ->setCreatedAt($this->clock->now())
            ->setCreatedBy($user)
        ;

        foreach ($this->organizations as $organization) {
            $project->addOrganization($organization);
        }

        foreach ($this->tags as $tag) {
            $project->addTag($tag);
        }

        $manager->persist($project);
        $manager->flush();

        $this->dispatchBrowserEvent('modal:close');
        $this->emit('project:created', ['project' => $project->getId()]);

        $this->name = '';
        $this->summary = '';
        $this->organizations = [];
        $this->tags = [];
        $this->resetValidation();

        if ($this->redirect) {
            return new RedirectResponse($this->urlGenerator->generate('app_project_show', ['id' => $project->getId()]));
        }
    }
}
