<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PagesLegalesController extends AbstractController
{
    #[Route('/conditions-generales-de-vente', name: 'app_conditions_generales_de_vente')]
    public function conditionsGeneralesDeVente(): Response
    {
        return $this->render('pages-legales/conditions-generales-de-vente.html.twig', [
            'controller_name' => 'PagesLegalesController',
        ]);
    }

    #[Route('/mentions-legales', name: 'app_mentions_legales')]
    public function mentionsLegales(): Response
    {
        return $this->render('pages-legales/mentions-legales.html.twig', [
            'controller_name' => 'PagesLegalesController',
        ]);
    }
}
