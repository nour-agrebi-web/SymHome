<?php

namespace App\Controller;

use App\Entity\Meuble;
use App\Form\MeubleType;
use App\Repository\MeubleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/meuble')]
final class MeubleController extends AbstractController
{
    #[Route(name: 'app_meuble_index', methods: ['GET'])]
    public function index(MeubleRepository $meubleRepository): Response
    {
        return $this->render('meuble/index.html.twig', [
            'meubles' => $meubleRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_meuble_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $meuble = new Meuble();
        $form = $this->createForm(MeubleType::class, $meuble);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($meuble);
            $entityManager->flush();

            return $this->redirectToRoute('app_meuble_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('meuble/new.html.twig', [
            'meuble' => $meuble,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_meuble_show', methods: ['GET'])]
    public function show(Meuble $meuble): Response
    {
        return $this->render('meuble/show.html.twig', [
            'meuble' => $meuble,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_meuble_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Meuble $meuble, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(MeubleType::class, $meuble);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_meuble_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('meuble/edit.html.twig', [
            'meuble' => $meuble,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_meuble_delete', methods: ['POST'])]
    public function delete(Request $request, Meuble $meuble, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$meuble->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($meuble);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_meuble_index', [], Response::HTTP_SEE_OTHER);
    }
}
