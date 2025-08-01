<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class MainController extends AbstractController
{
    /*
    #[Route('/', name: 'index')]
    public function index(): Response
    {
        return $this->render('main/home.html.twig', [

        ]);
    }*/

    #[Route('/', name: 'home', methods: ['GET'])]
    public function home(Security $security): Response
    {
        $user = $security->getUser();

        if (!$user) {
            // Utilisateur non connecté, affiche le formulaire de login
            return $this->redirectToRoute('app_login');
        }

        // Utilisateur connecté, affiche le contenu de l'accueil
        return $this->render('main/home.html.twig', [
            'user' => $user,
        ]);
    }
}
