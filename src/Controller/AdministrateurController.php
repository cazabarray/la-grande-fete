<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\UserRepository;

final class AdministrateurController extends AbstractController
{
    #[Route('/administrateur', name: 'app_administrateur')]
    public function index(): Response
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            return $this->render('administrateur/index.html.twig', [
                'controller_name' => 'AdministrateurController',
            ]);
        }
        else {
            return $this->redirectToRoute('app_accueil');
        }
    }

    #[Route('/administrateur/utilisateurs', name: 'app_administrateur_utilisateurs')]
    public function users(UserRepository $userRepo): Response
    {
        $users = $userRepo->findAll();

        return $this->render('admin/users.html.twig', [
            'users' => $users,
        ]);
    }

}
