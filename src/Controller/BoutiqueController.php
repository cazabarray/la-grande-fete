<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\ArticleRepository;
use App\Entity\Article;

#[Route('/boutique')]
final class BoutiqueController extends AbstractController
{
    #[Route(name: 'app_boutique')]
    public function index(Request $request, ArticleRepository $repo): Response
    {
        $type = $request->query->get('type');

        if ($type) {
            $articles = $repo->findBy(['type' => $type]);
        } else {
            $articles = $repo->findAll();
        }

        return $this->render('boutique/index.html.twig', [
            'articles' => $articles,
            'typeActif' => $type,
        ]);
    }

    #[Route('/{id}', name: 'app_boutique_article')]
    public function article(Article $article): Response
    {
        return $this->render('boutique/article.html.twig', [
            'article' => $article,
        ]);
    }
}
