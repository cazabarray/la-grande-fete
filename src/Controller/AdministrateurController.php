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

        return $this->render('administrateur/utilisateurs.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/administrateur/utilisateur/{id}/promotion', name: 'app_administrateur_utilisateur_promotion')]
    public function promotionUtilisateur(User $user, EntityManagerInterface $em): Response
    {
        if (!in_array('ROLE_ADMIN', $user->getRoles())) {
            $user->setRoles(array_merge($user->getRoles(), ['ROLE_ADMIN']));
            $em->flush();
            $this->addFlash('success', 'Utilisateur promu en administrateur.');
        }

        return $this->redirectToRoute('app_administrateur_utilisateurs');
    }

    #[Route('/administrateur/utilisateur/{id}/retrogradation', name: 'app_administrateur_utilisateur_retrogradation')]
    public function retrogradationUtilisateur(User $user, EntityManagerInterface $em): Response
    {
        $newRoles = array_filter($user->getRoles(), fn($r) => $r !== 'ROLE_ADMIN');

        $user->setRoles($newRoles);
        $em->flush();

        $this->addFlash('success', 'Administrateur rétrogradé en utilisateur.');

        return $this->redirectToRoute('app_administrateur_utilisateurs');
    }

    #[Route('/administrateur/utilisateur/{id}/suppression', name: 'app_administrateur_utilisateur_suppression', methods: ['POST'])]
    public function suppressionUtilisateur(User $user,Request $request, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete-user-' . $user->getId(), $request->request->get('_token'))) {
            $em->remove($user);
            $em->flush();
            $this->addFlash('success', 'Utilisateur supprimé avec succès.');
        }

        return $this->redirectToRoute('app_administrateur_utilisateurs');
    }

    #[Route('/administrateur/tickets', name: 'app_administrateur_tickets')]
    public function tickets(TicketRepository $ticketRepo): Response
    {
        $tickets = $ticketRepo->findBy([], ['id' => 'DESC']);

        return $this->render('administrateur/tickets.html.twig', [
            'tickets' => $tickets,
        ]);
    }

    #[Route('/admin/ticket/{id}/modification', name: 'app_administrateur_ticket_modification')]
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
