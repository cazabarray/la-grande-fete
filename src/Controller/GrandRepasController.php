<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class GrandRepasController extends AbstractController
{
    #[Route('/grand-repas', name: 'app_grand_repas')]
    public function index(): Response
    {
        return $this->render('grand-repas/index.html.twig', [
            'controller_name' => 'GrandRepasController',
        ]);
    }
}
