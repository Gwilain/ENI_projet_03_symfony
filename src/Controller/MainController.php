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

        $defaultCampus = $user->getCampus();

        $form = $this->createForm(FiltersType::class, ['campus' => $defaultCampus]);
        $form->handleRequest($request);
       // if ($form->isSubmitted() && $form->isValid()) {

            $filters = $form->getData();
            $filters['user'] = $this->getUser();
//            $selectedOptions = $data['sortiesQue'] ?? [];

            $sorties = $sortieRepository->findByFilters($filters);

        //}


        //redirect connected user to login
        return $this->render('main/home.html.twig', [
            'form' => $form->createView(),
            'sorties' => $sorties,
        ]);
    }
}
