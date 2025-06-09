<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\VolunteerProfile;
use App\Form\AddressType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    public function index(#[CurrentUser] User $user, EntityManagerInterface $entityManager): Response
    {
        if (!$user->getVolunteerProfile()) {
            $user->setVolunteerProfile((new VolunteerProfile())->setForUser($user));
            $entityManager->flush();
        }

        return $this->render('profile/index.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/profile/address', name: 'app_profile_address')]
    #[IsGranted('ROLE_USER')]
    public function editAddress(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $profile = $user->getVolunteerProfile() ?? new VolunteerProfile();

        if (!$user->getVolunteerProfile()) {
            $profile->setForUser($user);
            $entityManager->persist($profile);
        }

        $form = $this->createForm(AddressType::class, $profile);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Your address has been updated successfully.');

            return $this->redirectToRoute('app_profile_address');
        }

        return $this->render('profile/address.html.twig', [
            'form' => $form,
        ]);
    }
}
