<?php

namespace App\Twig\Components;

use App\Entity\Tag;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\ValidatableComponentTrait;

#[AsLiveComponent]
final class NewTagForm
{
    use ComponentToolsTrait;
    use DefaultActionTrait;
    use ValidatableComponentTrait;

    #[LiveProp(writable: true)]
    #[NotBlank]
    public string $name = '';

    #[LiveAction]
    public function saveTag(EntityManagerInterface $manager): void
    {
        $this->validate();

        $tag = (new Tag())
            ->setName($this->name);
        $manager->persist($tag);
        $manager->flush();

        $this->dispatchBrowserEvent('modal:close');
        $this->emit('tag:created', ['tag' => $tag->getId()]);

        $this->name = '';
        $this->resetValidation();
    }
}
