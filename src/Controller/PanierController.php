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
        $panierComplet = [];
        $total = 0;

        foreach ($panier as $cle => $ligne) {
            $article = $repo->find($ligne['id']);

            if ($article) {
                $sousTotal = $article->getPrix() * $ligne['quantite'];
                $total += $sousTotal;

                $panierComplet[] = [
                    'article' => $article,
                    'taille' => $ligne['taille'],
                    'quantite' => $ligne['quantite'],
                    'sousTotal' => $sousTotal,
                ];
            }
        }

        return $this->render('panier/index.html.twig', [
            'panier' => $panierComplet,
            'total' => $total,
        ]);
    }

    #[Route('/ajout/{id}', name: 'app_panier_ajout', methods: ['POST'])]
    public function ajouter(int $id, Request $request): Response
    {
        $taille = $request->request->get('taille');
        $session = $request->getSession();
        $panier = $session->get('panier', []);

        $cle = $id . '-' . $taille;

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
