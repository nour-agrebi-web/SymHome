<?php
namespace App\Controller;
use App\Entity\Meuble;
use App\Form\MeubleType;
use App\Repository\MeubleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
#[Route('/meuble')]
class MeubleController extends AbstractController
{
    #[Route('/', name: 'app_meuble_index', methods: ['GET'])]
    public function index(MeubleRepository $meubleRepository): Response
    {
        return $this->render('meuble/index.html.twig', [
            'meubles' => $meubleRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_meuble_new', methods: ['GET', 'POST'])]
    public function new(Request $request, MeubleRepository $meubleRepository): Response
    {
        $meuble = new Meuble();
        $form = $this->createForm(MeubleType::class, $meuble);
        $form->handleRequest($request); 
        if ($form->isSubmitted() && $form->isValid()) {
            $meubleRepository->save($meuble, true);
            return $this->redirectToRoute('app_meuble_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('meuble/new.html.twig', [
            'meuble' => $meuble,
            'form' => $form,
        ]);

    }
    

}