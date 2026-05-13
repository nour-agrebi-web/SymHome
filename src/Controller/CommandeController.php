<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\LigneCommande;
use App\Repository\CommandeRepository;
use App\Repository\MeubleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CommandeController extends AbstractController
{
    public function __construct(
        private RequestStack $requestStack
    ) {
    }

    #[Route('/commande/checkout', name: 'app_commande_checkout')]
   public function checkout(
    MeubleRepository $meubleRepository,
    EntityManagerInterface $entityManager
): RedirectResponse {
        $session = $this->requestStack->getSession();
        $panier = $session->get('panier', []);

        if (empty($panier)) {
            $this->addFlash('danger', 'Votre panier est vide.');
            return $this->redirectToRoute('app_panier_index');
        }

       
      $user = $this->getUser();

        if (!$user) {
            $this->addFlash('danger', 'Vous devez vous connecter pour valider une commande.');
            return $this->redirectToRoute('app_login');
        }

        $commande = new Commande();
        $commande->setUser($user);
        $commande->setNumero('CMD-' . date('YmdHis') . '-' . random_int(100, 999));
        $commande->setEtat(Commande::ETAT_EN_ATTENTE);
        $commande->setModePaiement('paiement_simule');
        $commande->setIsPaid(true);

        $total = 0;

        foreach ($panier as $meubleId => $quantite) {
            $meuble = $meubleRepository->find($meubleId);

            if (!$meuble) {
                continue;
            }

            $prixUnitaire = (float) $meuble->getPrix();
            $sousTotal = $prixUnitaire * $quantite;
            $total += $sousTotal;

            $ligneCommande = new LigneCommande();
            $ligneCommande->setCommande($commande);
            $ligneCommande->setMeuble($meuble);
            $ligneCommande->setQuantite($quantite);
            $ligneCommande->setPrixUnitaire((string) $prixUnitaire);
            $ligneCommande->setSousTotal((string) $sousTotal);

            $commande->addLigneCommande($ligneCommande);

            if ($meuble->getStock() !== null && $meuble->getStock() >= $quantite) {
                $meuble->setStock($meuble->getStock() - $quantite);
            }

            $entityManager->persist($ligneCommande);
        }

        $commande->setTotal((string) $total);

        $entityManager->persist($commande);
        $entityManager->flush();

        $session->remove('panier');

        $this->addFlash('success', 'Commande validée avec succès.');

        return $this->redirectToRoute('app_commande_confirmation', [
            'id' => $commande->getId(),
        ]);
    }

    #[Route('/commande/confirmation/{id}', name: 'app_commande_confirmation')]
    public function confirmation(Commande $commande): Response
    {
        return $this->render('commande/confirmation.html.twig', [
            'commande' => $commande,
        ]);
    }

    #[Route('/mes-commandes', name: 'app_commande_historique')]
    public function historique(CommandeRepository $commandeRepository): Response
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $commandes = $commandeRepository->findBy(
            ['user' => $user],
            ['id' => 'DESC']
        );

        return $this->render('commande/historique.html.twig', [
            'commandes' => $commandes,
        ]);
    }
#[Route('/mes-commandes/{id}', name: 'app_commande_detail')]
    public function detail(Commande $commande): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        if ($commande->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Accès refusé.');
        }

        return $this->render('commande/detail.html.twig', [
            'commande' => $commande,
        ]);
    }
}