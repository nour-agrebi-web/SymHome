<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\LigneCommande;
use App\Entity\User;
use App\Repository\CommandeRepository;
use App\Repository\MeubleRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
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
        UserRepository $userRepository,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): RedirectResponse {
        $session = $this->requestStack->getSession();
        $panier = $session->get('panier', []);

        if (empty($panier)) {
            $this->addFlash('danger', 'Votre panier est vide.');
            return $this->redirectToRoute('app_panier_index');
        }

        /*
         * Version temporaire sans login/register:
         * On utilise un client demo pour pouvoir tester les commandes.
         * Après, quand tu fais login/register, on remplace cette partie par $this->getUser().
         */
        $user = $userRepository->findOneBy(['email' => 'client@symhome.com']);

        if (!$user) {
            $user = new User();
            $user->setEmail('client@symhome.com');
            $user->setRoles(['ROLE_USER']);
            $user->setNom('Client');
            $user->setPrenom('Demo');
            $user->setTelephone('00000000');
            $user->setAdresse('Adresse demo');
            $user->setIsVerified(true);

            $hashedPassword = $passwordHasher->hashPassword($user, 'client123');
            $user->setPassword($hashedPassword);

            $entityManager->persist($user);
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
        /*
         * Version temporaire:
         * On affiche toutes les commandes.
         * Après login/register, on filtrera par user connecté.
         */
        $commandes = $commandeRepository->findBy([], [
            'id' => 'DESC',
        ]);

        return $this->render('commande/historique.html.twig', [
            'commandes' => $commandes,
        ]);
    }

    #[Route('/mes-commandes/{id}', name: 'app_commande_detail')]
    public function detail(Commande $commande): Response
    {
        return $this->render('commande/detail.html.twig', [
            'commande' => $commande,
        ]);
    }
}