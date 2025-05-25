<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\ArticleRepository;

#[Route('/panier')]
final class PanierController extends AbstractController
{
    #[Route(name: 'app_panier')]
    public function index(Request $request, ArticleRepository $repo): Response
    {
        $panier = $request->getSession()->get('panier', []);
        $articles = [];

        foreach ($panier as $id => $quantite) {
            $article = $repo->find($id);
            if ($article) {
                $articles[] = [
                    'article' => $article,
                    'quantite' => $quantite
                ];
            }
        }

        return $this->render('panier/index.html.twig', [
            'articles' => $articles
        ]);
    }


    #[Route('/ajout/{id}', name: 'app_panier_ajout')]
    public function ajout(Request $request, int $id): Response
    {
        $panier = $request->getSession()->get('panier', []);
        
        if (array_key_exists($id, $panier)) {
            $panier[$id]++;
        } else {
            $panier[$id] = 1;
        }

        $request->getSession()->set('panier', $panier);

        $this->addFlash('success', 'Article ajouté au panier avec succès.');

        return $this->redirectToRoute('app_panier');
    }

    #[Route('/suppression/{id}', name: 'app_panier_suppression')]
    public function suppression(Request $request, int $id): Response
    {
        $session = $request->getSession();
        $panier = $session->get('panier', []);

        if (array_key_exists($id, $panier)) {
            unset($panier[$id]);
            $session->set('panier', $panier);
            $this->addFlash('success', 'Article retiré du panier avec succès.');
        }

        return $this->redirectToRoute('app_panier');
    }
}
