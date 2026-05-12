<?php

namespace App\Controller;

use App\Entity\Meuble;
use App\Repository\CategorieRepository;
use App\Repository\MeubleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ProduitController extends AbstractController
{
    #[Route('/produits', name: 'app_produit_index')]
    public function index(
        Request $request,
        MeubleRepository $meubleRepository,
        CategorieRepository $categorieRepository
    ): Response {
        $search = $request->query->get('search');
        $categorieId = $request->query->get('categorie');

        $queryBuilder = $meubleRepository->createQueryBuilder('m')
            ->leftJoin('m.categorie', 'c')
            ->addSelect('c');

        if ($search) {
            $queryBuilder
                ->andWhere('m.nom LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }

        if ($categorieId) {
            $queryBuilder
                ->andWhere('c.id = :categorieId')
                ->setParameter('categorieId', $categorieId);
        }

        $meubles = $queryBuilder
            ->orderBy('m.id', 'DESC')
            ->getQuery()
            ->getResult();

        return $this->render('produit/index.html.twig', [
            'meubles' => $meubles,
            'categories' => $categorieRepository->findAll(),
            'search' => $search,
            'categorieId' => $categorieId,
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
