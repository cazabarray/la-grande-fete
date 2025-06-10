<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class InformationsPratiquesController extends AbstractController
{
    #[Route('/informations-pratiques', name: 'app_informations_pratiques')]
    public function index(): Response
    {
        return $this->render('informations-pratiques/index.html.twig', [
            'controller_name' => 'InformationsPratiquesController',
        ]);
    }
}
