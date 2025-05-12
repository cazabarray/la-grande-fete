<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\TicketRepository;
use Symfony\Component\Security\Core\Security;
use App\Entity\Ticket;
use App\Form\TicketForm;

final class SupportController extends AbstractController
{
    #[Route('/support', name: 'app_support')]
    public function index(Request $request, EntityManagerInterface $em, TicketRepository $repo, Security $security): Response
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
            }
            else {
                return $this->redirectToRoute('app_login');
            }
        }

        $tickets = $repo->findBy(['user' => $user], ['id' => 'DESC']);

        return $this->render('support/index.html.twig', [
            'form' => $form->createView(),
            'tickets' => $tickets,
        ]);
    }
}
