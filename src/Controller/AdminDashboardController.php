<?php

namespace App\Controller;

use App\Repository\CommandeRepository;
use App\Repository\LigneCommandeRepository;
use App\Repository\MeubleRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/dashboard')]
final class AdminDashboardController extends AbstractController
{
    #[Route('/', name: 'app_admin_dashboard')]
    public function index(
        UserRepository $userRepository,
        MeubleRepository $meubleRepository,
        CommandeRepository $commandeRepository,
        LigneCommandeRepository $ligneCommandeRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $nombreClients = count($userRepository->findAll());
        $nombreMeubles = count($meubleRepository->findAll());
        $nombreCommandes = count($commandeRepository->findAll());

        $commandes = $commandeRepository->findAll();

        $chiffreAffaires = 0;
        $commandesEnAttente = 0;
        $commandesEnCours = 0;
        $commandesCompletees = 0;

        foreach ($commandes as $commande) {
            if ($commande->isPaid()) {
                $chiffreAffaires += (float) $commande->getTotal();
            }

            if ($commande->getEtat() === 'en_attente') {
                $commandesEnAttente++;
            } elseif ($commande->getEtat() === 'en_cours') {
                $commandesEnCours++;
            } elseif ($commande->getEtat() === 'completee') {
                $commandesCompletees++;
            }
        }

        $result = $entityManager->createQueryBuilder()
            ->select('m.nom AS nom, SUM(l.quantite) AS totalVendu')
            ->from('App\Entity\LigneCommande', 'l')
            ->join('l.meuble', 'm')
            ->groupBy('m.id')
            ->orderBy('totalVendu', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        $meublePlusVendu = $result ? $result['nom'] : 'Aucun';

        return $this->render('admin_dashboard/index.html.twig', [
            'nombreClients' => $nombreClients,
            'nombreMeubles' => $nombreMeubles,
            'nombreCommandes' => $nombreCommandes,
            'chiffreAffaires' => $chiffreAffaires,
            'meublePlusVendu' => $meublePlusVendu,
            'commandesEnAttente' => $commandesEnAttente,
            'commandesEnCours' => $commandesEnCours,
            'commandesCompletees' => $commandesCompletees,
        ]);
    }
}
