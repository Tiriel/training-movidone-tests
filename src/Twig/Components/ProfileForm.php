<?php

namespace App\Twig\Components;

use App\Entity\Tag;
use App\Entity\User;
use App\Entity\VolunteerProfile;
use App\Form\VolunteerProfileType;
use Cassandra\Type\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveListener;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\ValidatableComponentTrait;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

#[AsLiveComponent]
final class ProfileForm extends AbstractController
{
    use DefaultActionTrait;
    use ValidatableComponentTrait;
    use ComponentWithFormTrait;

    #[LiveProp]
    public ?VolunteerProfile $profile = null;

    #[LiveProp]
    public bool $isEditing = false;

    public ?string $flash = null;

    public function __construct(
        private readonly EntityManagerInterface $manager,
    ) {}

    #[LiveListener('tag:created')]
    public function onTagCreated(#[LiveArg] Tag $tag): void
    {
        $this->formValues['interests'][] = $tag->getId();
    }

    public function isTagSelected(Tag $tag): bool
    {
        return $this->profile->getInterests()->contains($tag);
    }

    #[LiveAction]
    public function editProfile(): void
    {
        $this->isEditing = true;
    }

    #[LiveAction]
    public function save(EntityManagerInterface $manager): RedirectResponse
    {
        $this->submitForm();

        $this->isEditing = false;
        $manager->persist($this->profile);
        $manager->flush();
        $this->addFlash('success', 'Profile saved.');

        return $this->redirectToRoute('app_profile');
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(VolunteerProfileType::class, $this->profile);
    }
}
