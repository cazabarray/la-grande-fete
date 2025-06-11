<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\UserRepository;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\ArticleRepository;
use App\Entity\Article;
use App\Form\ArticleForm;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Repository\TicketRepository;
use App\Entity\Ticket;

#[Route('/acces-administrateur')]
final class AccesAdministrateurController extends AbstractController
{
    #[Route(name: 'app_acces_administrateur')]
    public function index(): Response
    {
        return $this->render('acces-administrateur/index.html.twig', [
            'controller_name' => 'AccesAdministrateurController',
        ]);
    }

    #[Route('/utilisateurs', name: 'app_acces_administrateur_utilisateurs')]
    public function utilisateurs(UserRepository $userRepo): Response
    {
        $users = $userRepo->findAll();

        return $this->render('acces-administrateur/utilisateurs.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/utilisateur/{id}/promotion', name: 'app_acces_administrateur_utilisateur_promotion')]
    public function promotionUtilisateur(User $user, EntityManagerInterface $em): Response
    {
        if (!in_array('ROLE_ADMIN', $user->getRoles())) {
            $user->setRoles(array_merge($user->getRoles(), ['ROLE_ADMIN']));
            $em->flush();

            $this->addFlash('success', 'Utilisateur promu en administrateur avec succès.');
        }

        return $this->redirectToRoute('app_acces_administrateur_utilisateurs');
    }

    #[Route('/utilisateur/{id}/retrogradation', name: 'app_acces_administrateur_utilisateur_retrogradation')]
    public function retrogradationUtilisateur(User $user, EntityManagerInterface $em): Response
    {
        $newRoles = array_filter($user->getRoles(), fn($r) => $r !== 'ROLE_ADMIN');
        $user->setRoles($newRoles);
        $em->flush();

        $this->addFlash('success', 'Administrateur rétrogradé en utilisateur avec succès.');

        return $this->redirectToRoute('app_acces_administrateur_utilisateurs');
    }

    #[Route('/utilisateur/{id}/suppression', name: 'app_acces_administrateur_utilisateur_suppression', methods: ['POST'])]
    public function suppressionUtilisateur(User $user, Request $request, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete-user-' . $user->getId(), $request->request->get('_token'))) {
            $em->remove($user);
            $em->flush();

            $this->addFlash('success', 'Utilisateur supprimé avec succès.');
        }

        return $this->redirectToRoute('app_acces_administrateur_utilisateurs');
    }

    #[Route('/articles', name: 'app_acces_administrateur_articles')]
    public function articles(ArticleRepository $repo): Response
    {
        $articles = $repo->findAll();

        return $this->render('acces-administrateur/articles/index.html.twig', [
            'articles' => $articles,
        ]);
    }

    #[Route('/article/ajout', name: 'app_acces_administrateur_article_ajout')]
    public function ajoutArticle(Request $request, EntityManagerInterface $em): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleForm::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('image')->getData();

            if ($image instanceof UploadedFile) {
                $nomFichier = uniqid() . '.' . $image->guessExtension();
                $image->move($this->getParameter('uploads_directory'), $nomFichier);
                $article->setImage($nomFichier);
            }

            $em->persist($article);
            $em->flush();

            $this->addFlash('success', 'Article ajouté avec succès.');

            return $this->redirectToRoute('app_administrateur_articles');
        }

        return $this->render('acces-administrateur/articles/form.html.twig', [
            'form' => $form->createView(),
            'article' => $article,
        ]);
    }

    #[Route('/article/{id}', name: 'app_acces_administrateur_article_modification')]
    public function modificationArticle(Article $article, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(ArticleForm::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('image')->getData();

            if ($image instanceof UploadedFile) {
                $ancienFichier = $article->getImage();
                $nomFichier = uniqid() . '.' . $image->guessExtension();
                $image->move($this->getParameter('uploads_directory'), $nomFichier);
                $article->setImage($nomFichier);

                if ($ancienFichier) {
                    $chemin = $this->getParameter('uploads_directory') . '/' . $ancienFichier;

                    if (file_exists($chemin)) {
                        unlink($chemin);
                    }
                }
            }

            $em->flush();

            $this->addFlash('success', 'Article modifié avec succès.');

            return $this->redirectToRoute('app_administrateur_articles');
        }

        return $this->render('acces-administrateur/articles/form.html.twig', [
            'form' => $form->createView(),
            'article' => $article,
        ]);
    }

    #[Route('/article/{id}/suppression', name: 'app_acces_administrateur_article_suppression', methods: ['POST'])]
    public function suppressionArticle(Article $article, Request $request, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('suppression-article' . $article->getId(), $request->request->get('_token'))) {
            $image = $article->getImage();

            if ($image) {
                $chemin = $this->getParameter('uploads_directory') . '/' . $image;
                
                if (file_exists($chemin)) {
                    unlink($chemin);
                }
            }

            $em->remove($article);
            $em->flush();

            $this->addFlash('success', 'Article supprimé avec succès.');
        }

        return $this->redirectToRoute('app_acces_administrateur_articles');
    }

    #[Route('articles/types', name: 'app_acces_administrateur_articles_types')]
    public function typesArticles(\App\Repository\TypeRepository $typeRepo): Response
    {
        $types = $typeRepo->findAll();

        return $this->render('acces-administrateur/articles/types/index.html.twig', [
            'types' => $types,
        ]);
    }

    #[Route('articles/type/ajout', name: 'app_acces_administrateur_articles_type_ajout')]
    public function ajoutTypeArticles(Request $request, EntityManagerInterface $em): Response
    {
        $type = new \App\Entity\Type();
        $form = $this->createForm(\App\Form\TypeForm::class, $type);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($type);
            $em->flush();

            $this->addFlash('success', 'Type ajouté avec succès.');

            return $this->redirectToRoute('app_acces_administrateur_articles_types');
        }

        return $this->render('acces-administrateur/articles/types/form.html.twig', [
            'form' => $form->createView(),
            'type' => $type,
        ]);
    }

    #[Route('articles/type/{id}', name: 'app_acces_administrateur_articles_type_modification')]
    public function modificationTypeArticles(\App\Entity\Type $type, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(\App\Form\TypeForm::class, $type);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Type modifié avec succès.');

            return $this->redirectToRoute('app_administrateur_articles_types');
        }

        return $this->render('acces-administrateur/articles/types/form.html.twig', [
            'form' => $form->createView(),
            'type' => $type,
        ]);
    }

    #[Route('articles/type/{id}/suppression', name: 'app_acces_administrateur_articles_type_suppression', methods: ['POST'])]
    public function suppressionTypeArticles(\App\Entity\Type $type, Request $request, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('suppression-type' . $type->getId(), $request->request->get('_token'))) {
            $em->remove($type);
            $em->flush();

            $this->addFlash('success', 'Type supprimé avec succès.');
        }
        return $this->redirectToRoute('app_acces_administrateur_articles_types');
    }

    #[Route('/tickets', name: 'app_acces_administrateur_tickets')]
    public function tickets(TicketRepository $ticketRepo): Response
    {
        $tickets = $ticketRepo->findBy([], ['id' => 'DESC']);

        return $this->render('acces-administrateur/tickets.html.twig', [
            'tickets' => $tickets,
        ]);
    }

    #[Route('/ticket/{id}', name: 'app_acces_administrateur_ticket_modification')]
    public function modificationTicket(Ticket $ticket, EntityManagerInterface $em): Response
    {
        $ticket->setStatut(new \DateTime());
        $em->flush();

        $this->addFlash('success', 'Statut du ticket modifié avec succès.');

        return $this->redirectToRoute('app_acces_administrateur_tickets');
    }

    #[Route('/ticket/{id}/suppression', name: 'app_acces_administrateur_ticket_suppression', methods: ['POST'])]
    public function suppressionTicket(Ticket $ticket, Request $request, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('suppression-ticket' . $ticket->getId(), $request->request->get('_token'))) {
            $em->remove($ticket);
            $em->flush();

            $this->addFlash('success', 'Ticket supprimé avec succès.');
        }

        return $this->redirectToRoute('app_acces_administrateur_tickets');
    }
}
