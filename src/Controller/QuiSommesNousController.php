<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class QuiSommesNousController extends AbstractController
{
    #[Route('/qui-sommes-nous', name: 'app_qui_sommes_nous')]
    public function index(): Response
    {
        return $this->render('qui-sommes-nous/index.html.twig', [
            'controller_name' => 'QuiSommesNousController',
        ]);
    }
}
