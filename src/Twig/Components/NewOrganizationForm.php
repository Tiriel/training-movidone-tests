<?php

namespace App\Twig\Components;

use App\Entity\Organization;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Clock\ClockAwareTrait;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\ValidatableComponentTrait;

#[AsLiveComponent]
final class NewOrganizationForm
{
    use ClockAwareTrait;
    use ComponentToolsTrait;
    use DefaultActionTrait;
    use ValidatableComponentTrait;

    #[LiveProp(writable: true)]
    #[Assert\NotBlank]
    public string $name = '';

    #[LiveProp(writable: true)]
    #[Assert\NotBlank()]
    #[Assert\Length(min: 30)]
    public string $presentation = '';

    public function __construct(
        private readonly EntityManagerInterface $manager,
    ) {}

    #[LiveAction]
    public function saveOrganization(#[CurrentUser] User $user, EntityManagerInterface $manager): void
    {
        $this->validate();

        $org = (new Organization())
            ->setName($this->name)
            ->setPresentation($this->presentation)
            ->setCreatedAt($this->clock->now())
            ->addUser($user)
        ;
        $manager->persist($org);
        $manager->flush();

        $this->dispatchBrowserEvent('modal:close');
        $this->emit('org:created', ['organization' => $org->getId()]);

        $this->name = '';
        $this->presentation = '';
        $this->resetValidation();
    }
}
