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
        $donneesPanier = [];
        $total = 0;

        foreach ($panier as $cle => $ligne) {
            $article = $repo->find($ligne['id']);

            if (!$article) continue;

            $sousTotal = $article->getPrix() * $ligne['quantite'];
            $total += $sousTotal;

            $donneesPanier[] = [
                'cle' => $cle,
                'article' => $article,
                'taille' => $ligne['taille'],
                'quantite' => $ligne['quantite'],
                'sousTotal' => $sousTotal,
            ];
        }

        return $this->render('panier/index.html.twig', [
            'panier' => $donneesPanier,
            'total' => $total,
        ]);
    }

    #[Route('/{id}/ajout', name: 'app_panier_ajout', methods: ['POST'])]
    public function ajout(int $id, Request $request, ArticleRepository $repo): Response
    {
        $article = $repo->find($id);
        $taille = $request->request->get('taille');

        if ($article->getType()->getNom() === 'Vêtement' && !$taille) {

            $this->addFlash('error', 'Vous devez sélectionner une taille.');

            return $this->redirectToRoute('app_boutique_article', ['id' => $id]);
        }
        
        if ($article->getType()->getNom() !== 'Vêtement') {
            $taille = null;
        }

        $cle = $id . '-' . ($taille ?? '');
        $session = $request->getSession();
        $panier = $session->get('panier', []);

        if (isset($panier[$cle])) {
            $panier[$cle]['quantite']++;
        } else {
            $panier[$cle] = [
                'id' => $id,
                'taille' => $taille,
                'quantite' => 1,
            ];
        }

        $session->set('panier', $panier);

        $this->addFlash('success', 'Article ajouté au panier avec succès.');

        return $this->redirectToRoute('app_panier');
    }

    #[Route('/{id}/suppression', name: 'app_panier_suppression')]
    public function suppression(int $id, Request $request): Response
    {
        $taille = $request->query->get('taille');
        $cle = $id . '-' . ($taille ?? '');
        $session = $request->getSession();
        $panier = $session->get('panier', []);

        if (isset($panier[$cle])) {
            unset($panier[$cle]);
            $session->set('panier', $panier);
            
            $this->addFlash('success', 'Article supprimé du panier avec succès.');
        }

        return $this->redirectToRoute('app_panier');
    }
}
