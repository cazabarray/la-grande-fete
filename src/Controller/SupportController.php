<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\TicketRepository;
use App\Entity\Ticket;
use App\Form\TicketForm;

#[Route('/support')]
final class SupportController extends AbstractController
{
    #[Route(name: 'app_support')]
    public function index(Security $security, Request $request, EntityManagerInterface $em, TicketRepository $repo): Response
    {
        $user = $security->getUser();
        $ticket = new Ticket();
        $form = $this->createForm(TicketForm::class, $ticket);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $ticket->setUser($user);
                $em->persist($ticket);
                $em->flush();

                return $this->redirectToRoute('app_support');
            } else {
                return $this->redirectToRoute('app_login');
            }
        }

        $tickets = $repo->findBy(['user' => $user], ['id' => 'DESC']);

        return $this->render('support/index.html.twig', [
            'form' => $form->createView(),
            'tickets' => $tickets,
        ]);
    }

    #[Route('/{id}', name: 'app_support_modification')]
    public function modification(Ticket $ticket, Security $security, Request $request, EntityManagerInterface $em): Response
    {
        if ($ticket->getUser() !== $security->getUser()) {
            throw $this->createAccessDeniedException();
        }

        if ($ticket->getStatut() !== null) {
            return $this->redirectToRoute('app_support');
        }

        $form = $this->createForm(TicketForm::class, $ticket);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Ticket modifié avec succès.');

            return $this->redirectToRoute('app_support');
        }

        return $this->render('support/modification.html.twig', [
            'form' => $form->createView(),
            'ticket' => $ticket,
        ]);
    }

    #[Route('/{id}/suppression', name: 'app_support_suppression', methods: ['POST'])]
    public function suppression(Security $security, Ticket $ticket, Request $request, EntityManagerInterface $em): Response
    {
        $user = $security->getUser();

        if ($ticket->getUser() !== $user) {

            $this->addFlash('error', 'Vous ne pouvez supprimer que vos tickets.');

            return $this->redirectToRoute('app_support');
        }

        if ($this->isCsrfTokenValid('delete-ticket-' . $ticket->getId(), $request->request->get('_token'))) {
            $em->remove($ticket);
            $em->flush();
            
            $this->addFlash('success', 'Ticket supprimé avec succès.');
        }

        return $this->redirectToRoute('app_support');
    }
}
