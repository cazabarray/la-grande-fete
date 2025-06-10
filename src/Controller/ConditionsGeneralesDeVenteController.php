<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ConditionsGeneralesDeVenteController extends AbstractController
{
    #[Route('/conditions-generales-de-vente', name: 'app_conditions_generales_de_vente')]
    public function index(): Response
    {
        return $this->render('conditions-generales-de-vente/index.html.twig', [
            'controller_name' => 'ConditionsGeneralesDeVenteController',
        ]);
    }
}
