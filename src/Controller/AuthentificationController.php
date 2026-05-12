<?php
namespace App\Controller;
use App\Entity\User;
use App\Form\UserType;  
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;  
use Symfony\Component\Routing\Annotation\Route;
#[Route('/authentification')]   
class AuthentificationController extends AbstractController
{
    #[Route('/', name: 'app_authentification_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('authentification/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_authentification_new', methods: ['GET', 'POST'])]
    public function new(Request $request, UserRepository $userRepository): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request); 
        if ($form->isSubmitted() && $form->isValid()) {
            $userRepository->save($user, true);
            return $this->redirectToRoute('app_authentification_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('authentification/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }   }