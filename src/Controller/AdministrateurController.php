<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\UserRepository;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\AdministrateurutilisateurForm;
use App\Repository\TicketRepository;
use App\Entity\Ticket;
use App\Form\AdministrateurticketForm;

final class AdministrateurController extends AbstractController
{
    #[Route('/administrateur', name: 'app_administrateur')]
    public function index(): Response
    {
        return $this->render('administrateur/index.html.twig', [
            'controller_name' => 'AdministrateurController',
        ]);
    }

    #[Route('/administrateur/utilisateurs', name: 'app_administrateur_utilisateurs')]
    public function utilisateurs(UserRepository $userRepo): Response
    {
        $users = $userRepo->findAll();

        return $this->render('administrateur/utilisateur/index.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/administrateur/utilisateur/{id}/modification', name: 'app_administrateur_utilisateur_modification')]
    public function modificationUtilisateur(User $user, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(AdministrateurutilisateurForm::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Utilisateur modifié avec succès.');
            return $this->redirectToRoute('app_administrateur_utilisateurs');
        }

        return $this->render('administrateur/utilisateur/modification.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }

    #[Route('/administrateur/utilisateur/{id}/suppression', name: 'app_administrateur_utilisateur_suppression', methods: ['POST'])]
    public function suppressionUtilisateur(User $user,Request $request, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete-user-' . $user->getId(), $request->request->get('_token'))) {
            $em->remove($user);
            $em->flush();
            $this->addFlash('success', 'Utilisateur supprimé avec succès.');
        }

        return $this->redirectToRoute('app_administrateur_tickets');
    }

    #[Route('/administrateur/tickets', name: 'app_administrateur_tickets')]
    public function tickets(TicketRepository $ticketRepo): Response
    {
        $tickets = $ticketRepo->findBy([], ['id' => 'DESC']);

        return $this->render('administrateur/ticket/index.html.twig', [
            'tickets' => $tickets,
        ]);
    }

    #[Route('/admin/ticket/{id}/modification', name: 'app_administrateur_ticket_modificationstatut')]
    public function modificationstatutTicket(Ticket $ticket, EntityManagerInterface $em): Response
    {
        $ticket->setStatut(new \DateTime());
        $em->flush();

        $this->addFlash('success', 'Statut du ticket modifié avec succès.');
        return $this->redirectToRoute('app_administrateur_tickets');
    }

    #[Route('/administrateur/ticket/{id}/suppression', name: 'app_administrateur_ticket_suppression', methods: ['POST'])]
    public function suppressionTicket(Ticket $ticket, Request $request, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete-ticket-' . $ticket->getId(), $request->request->get('_token'))) {
            $em->remove($ticket);
            $em->flush();
            $this->addFlash('success', 'Ticket supprimé avec succès.');
        }

        return $this->redirectToRoute('app_administrateur_tickets');
    }
}
