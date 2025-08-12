<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin')]
final class AdminController extends AbstractController
{

    #[Route('/', name: 'admin')]
    public function index(): Response
    {
        return $this->render('admin/admin.html.twig', [
        ]);
    }



    #[Route('/utilisateurs', name: 'users')]
    public function users(UserRepository $userRepo): Response
    {

        $allUsers = $userRepo->findAll();

        return $this->render('admin/users.html.twig', [
            'users' => $allUsers,
        ]);
    }
}
