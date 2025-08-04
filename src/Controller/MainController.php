<?php

namespace App\Controller;

use App\Form\FiltersType;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
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
    public function home(Request $request, Security $security,SortieRepository $sortieRepository): Response
    {
        $user = $security->getUser();

        if (!$user) {
            //redirect non donnected user to login
            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(FiltersType::class);
        $form->handleRequest($request);

        $filters = $form->getData();

        $sorties = $sortieRepository->findAll();

        //redirect connected user to login
        return $this->render('main/home.html.twig', [
            'form' => $form->createView(),
            'sorties' => $sorties,
        ]);
    }
}
