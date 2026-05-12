<?php

namespace App\Controller;

use App\Entity\Meuble;
use App\Repository\MeubleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ProduitController extends AbstractController
{
    #[Route('/produits', name: 'app_produit_index')]
    public function index(MeubleRepository $meubleRepository): Response
    {
        return $this->render('produit/index.html.twig', [
            'meubles' => $meubleRepository->findAll(),
        ]);
    }

    #[Route('/produit/{id}', name: 'app_produit_show')]
    public function show(Meuble $meuble): Response
    {
        return $this->render('produit/show.html.twig', [
            'meuble' => $meuble,
        ]);
    }
    #[Route('/produit/{id}', name: 'app_produit_show')]
public function show(Meuble $meuble): Response
{
    return $this->render('produit/show.html.twig', [
        'meuble' => $meuble,
    ]);
}
}
