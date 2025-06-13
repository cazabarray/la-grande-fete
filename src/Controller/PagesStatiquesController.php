<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PagesStatiquesController extends AbstractController
{
    #[Route('/', name: 'app_accueil')]
    public function accueil(): Response
    {
        return $this->render('pages-statiques/accueil.html.twig', [
            'controller_name' => 'PagesStatiquesController',
        ]);
    }

    #[Route('/programme', name: 'app_programme')]
    public function programme(): Response
    {
        return $this->render('pages-statiques/programme.html.twig', [
            'controller_name' => 'PagesStatiquesController',
        ]);
    }

    #[Route('/grand-repas', name: 'app_grand_repas')]
    public function grandRepas(): Response
    {
        return $this->render('pages-statiques/grand-repas.html.twig', [
            'controller_name' => 'PagesStatiquesController',
        ]);
    }

    #[Route('/informations-pratiques', name: 'app_informations_pratiques')]
    public function informationsPratiques(): Response
    {
        return $this->render('pages-statiques/informations-pratiques.html.twig', [
            'controller_name' => 'PagesStatiquesController',
        ]);
    }

    #[Route('/qui-sommes-nous', name: 'app_qui_sommes_nous')]
    public function quiSommesNous(): Response
    {
        return $this->render('pages-statiques/qui-sommes-nous.html.twig', [
            'controller_name' => 'PagesStatiquesController',
        ]);
    }

    #[Route('/partenaires', name: 'app_partenaires')]
    public function partenaires(): Response
    {
        return $this->render('pages-statiques/partenaires.html.twig', [
            'controller_name' => 'PagesStatiquesController',
        ]);
    }

    #[Route('/conditions-generales-de-vente', name: 'app_conditions_generales_de_vente')]
    public function conditionsGeneralesDeVente(): Response
    {
        return $this->render('pages-statiques/conditions-generales-de-vente.html.twig', [
            'controller_name' => 'PagesStatiquesController',
        ]);
    }

    #[Route('/mentions-legales', name: 'app_mentions_legales')]
    public function mentionsLegales(): Response
    {
        return $this->render('pages-statiques/mentions-legales.html.twig', [
            'controller_name' => 'PagesStatiquesController',
        ]);
    }
}
