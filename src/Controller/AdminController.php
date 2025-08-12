<?php

namespace App\Controller;

use App\Entity\Ville;
use App\Form\SortieType;
use App\Form\VilleType;
use App\Repository\UserRepository;
use App\Repository\VilleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin')]
final class AdminController extends AbstractController
{

    #[Route('/', name: 'admin')]
    public function admin(): Response
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
    #[Route('/villes', name: 'villes', methods: ['GET'])]
    public function villes(VilleRepository $villeRepo): Response
    {
        $allCities = $villeRepo->findAll();
        $citiesForms = [];

        // Formulaire d'ajout
        $formNew = $this->createForm(VilleType::class, new Ville(), ['editable' => true,]);

        // Formulaires d'édition
        foreach ($allCities as $city) {
            $citiesForms[$city->getId()] = $this->createForm(VilleType::class, $city)->createView();
        }

        return $this->render('admin/villes.html.twig', [
            'citiesForms' => $citiesForms,
            'newCitieForm' => $formNew->createView(),
            'cities' => $allCities,
        ]);
    }

    #[Route('/villes/new', name: 'villes_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $newCity = new Ville();
        $form = $this->createForm(VilleType::class, $newCity, ['editable' => true,]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($newCity);
            $em->flush();
        }

        return $this->redirectToRoute('villes');
    }

    #[Route('/villes/edit/{id}', name: 'villes_edit', methods: ['POST'])]
    public function edit(Request $request, Ville $city, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(VilleType::class, $city);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
        }

        return $this->redirectToRoute('villes');
    }


    #[Route('/villes/delete/{id}', name: 'villes_delete', methods: ['POST'])]
    public function delete(Request $request, Ville $city, EntityManagerInterface $em): Response
    {

        $em->remove($city);
//        $em->persist($city);
        $em->flush();


        return $this->redirectToRoute('villes');
    }

}
