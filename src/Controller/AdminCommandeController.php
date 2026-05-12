<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/commandes')]
final class AdminCommandeController extends AbstractController
{
    #[Route('/', name: 'app_admin_commande_index', methods: ['GET'])]
    public function index(CommandeRepository $commandeRepository): Response
    {
        $commandes = $commandeRepository->findBy([], [
            'id' => 'DESC',
        ]);

        return $this->render('admin_commande/index.html.twig', [
            'commandes' => $commandes,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_commande_show', methods: ['GET'])]
    public function show(Commande $commande): Response
    {
        return $this->render('admin_commande/show.html.twig', [
            'commande' => $commande,
        ]);
    }

    #[Route('/{id}/etat', name: 'app_admin_commande_etat', methods: ['POST'])]
    public function changerEtat(
        Request $request,
        Commande $commande,
        EntityManagerInterface $entityManager
    ): RedirectResponse {
        if (!$this->isCsrfTokenValid('etat' . $commande->getId(), $request->request->get('_token'))) {
            $this->addFlash('danger', 'Token CSRF invalide.');
            return $this->redirectToRoute('app_admin_commande_show', [
                'id' => $commande->getId(),
            ]);
        }

        $etat = $request->request->get('etat');

        $etatsAutorises = [
            Commande::ETAT_EN_ATTENTE,
            Commande::ETAT_EN_COURS,
            Commande::ETAT_COMPLETEE,
            Commande::ETAT_ANNULEE,
        ];

        if (!in_array($etat, $etatsAutorises, true)) {
            $this->addFlash('danger', 'État invalide.');
            return $this->redirectToRoute('app_admin_commande_show', [
                'id' => $commande->getId(),
            ]);
        }

        $commande->setEtat($etat);
        $entityManager->flush();

        $this->addFlash('success', 'État de la commande modifié avec succès.');

        return $this->redirectToRoute('app_admin_commande_show', [
            'id' => $commande->getId(),
        ]);
    }
}