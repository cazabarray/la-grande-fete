<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\ArticleRepository;
use App\Repository\TypeRepository;
use App\Entity\Article;

#[Route('/boutique')]
final class BoutiqueController extends AbstractController
{
    #[Route(name: 'app_boutique')]
    public function index(Request $request, ArticleRepository $repo, TypeRepository $typeRepo): Response
    {
        $typeId = $request->query->get('type');
        $types = $typeRepo->findAll();

        if ($typeId) {
            $articles = $repo->findBy(['type' => $typeId]);
        } else {
            $articles = $repo->findAll();
        }

        return $this->render('boutique/index.html.twig', [
            'articles' => $articles,
            'typeActif' => $typeId,
            'types' => $types,
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
