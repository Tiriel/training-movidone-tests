<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\VolunteerProfile;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

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
}
