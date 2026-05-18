<?php

namespace App\Controller;

use App\Repository\MeubleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PanierController extends AbstractController
{
    private RequestStack $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    #[Route('/panier', name: 'app_panier_index')]
    public function index(MeubleRepository $meubleRepository): Response
    {
        if (!$this->getUser()) {
            $this->addFlash('warning', 'Vous devez vous connecter pour consulter votre panier.');
            return $this->redirectToRoute('app_login');
        }

        $session = $this->requestStack->getSession();
        $panier = $session->get('panier', []);

        $items = [];
        $total = 0;

        foreach ($panier as $id => $quantite) {
            $meuble = $meubleRepository->find($id);

            if ($meuble) {
                $sousTotal = (float) $meuble->getPrix() * $quantite;

                $items[] = [
                    'meuble' => $meuble,
                    'quantite' => $quantite,
                    'sousTotal' => $sousTotal,
                ];

                $total += $sousTotal;
            }
        }

        return $this->render('panier/index.html.twig', [
            'items' => $items,
            'total' => $total,
        ]);
    }

    #[Route('/panier/ajouter/{id}', name: 'app_panier_ajouter')]
    public function ajouter(int $id): RedirectResponse
    {
        if (!$this->getUser()) {
            $this->addFlash('warning', 'Vous devez vous connecter pour ajouter un produit au panier.');
            return $this->redirectToRoute('app_login');
        }

        $session = $this->requestStack->getSession();
        $panier = $session->get('panier', []);

        if (!empty($panier[$id])) {
            $panier[$id]++;
        } else {
            $panier[$id] = 1;
        }

        $session->set('panier', $panier);

        $this->addFlash('success', 'Produit ajouté au panier.');

        return $this->redirectToRoute('app_panier_index');
    }

    #[Route('/panier/plus/{id}', name: 'app_panier_plus')]
    public function plus(int $id): RedirectResponse
    {
        if (!$this->getUser()) {
            $this->addFlash('warning', 'Vous devez vous connecter pour modifier votre panier.');
            return $this->redirectToRoute('app_login');
        }

        $session = $this->requestStack->getSession();
        $panier = $session->get('panier', []);

        if (!empty($panier[$id])) {
            $panier[$id]++;
        }

        $session->set('panier', $panier);

        return $this->redirectToRoute('app_panier_index');
    }

    #[Route('/panier/moins/{id}', name: 'app_panier_moins')]
    public function moins(int $id): RedirectResponse
    {
        if (!$this->getUser()) {
            $this->addFlash('warning', 'Vous devez vous connecter pour modifier votre panier.');
            return $this->redirectToRoute('app_login');
        }

        $session = $this->requestStack->getSession();
        $panier = $session->get('panier', []);

        if (!empty($panier[$id])) {
            $panier[$id]--;

            if ($panier[$id] <= 0) {
                unset($panier[$id]);
            }
        }

        $session->set('panier', $panier);

        return $this->redirectToRoute('app_panier_index');
    }

    #[Route('/panier/supprimer/{id}', name: 'app_panier_supprimer')]
    public function supprimer(int $id): RedirectResponse
    {
        if (!$this->getUser()) {
            $this->addFlash('warning', 'Vous devez vous connecter pour modifier votre panier.');
            return $this->redirectToRoute('app_login');
        }

        $session = $this->requestStack->getSession();
        $panier = $session->get('panier', []);

        if (!empty($panier[$id])) {
            unset($panier[$id]);
        }

        $session->set('panier', $panier);

        $this->addFlash('success', 'Produit supprimé du panier.');

        return $this->redirectToRoute('app_panier_index');
    }

    #[Route('/panier/vider', name: 'app_panier_vider')]
    public function vider(): RedirectResponse
    {
        if (!$this->getUser()) {
            $this->addFlash('warning', 'Vous devez vous connecter pour vider votre panier.');
            return $this->redirectToRoute('app_login');
        }

        $session = $this->requestStack->getSession();
        $session->remove('panier');

        $this->addFlash('success', 'Panier vidé avec succès.');

        return $this->redirectToRoute('app_panier_index');
    }
}