<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Entity\Ville;
use App\Form\CampusType;
use App\Form\SortieType;
use App\Form\VilleType;
use App\Repository\CampusRepository;
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
        $allCities = $villeRepo->findBy([], ['name' => 'ASC']);
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

    #[Route('/villes/new', name: 'villes_new', methods: ['GET','POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $newCity = new Ville();
        $form = $this->createForm(VilleType::class, $newCity, [
            'method' => 'POST',
            'editable' => true,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->persist($newCity);
            $em->flush();

            $this->addFlash("success", "La ville a bien été ajouté !");
        }

        return $this->redirectToRoute('villes');
    }

    #[Route('/villes/edit/{id}', name: 'villes_edit', methods: ['GET','POST'])]
    public function edit(Request $request, Ville $city, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(VilleType::class, $city);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash("success", "La ville a bien été modifiée !");
        }

        return $this->redirectToRoute('villes');
    }


    #[Route('/villes/delete/{id}', name: 'villes_delete', methods: ['GET','POST'])]
    public function delete(Request $request, Ville $city, EntityManagerInterface $em): Response
    {

        $em->remove($city);
//        $em->persist($city);
        $em->flush();
        $this->addFlash("success", "La ville a bien été supprimée !");

        return $this->redirectToRoute('villes');
    }

    /************************************************************/
    /***********************CAMPUS*******************************/
    /************************************************************/

    #[Route('/campus', name: 'campus', methods: ['GET'])]
    public function campus(CampusRepository $campusRepo): Response
    {
        $allCampus = $campusRepo->findBy([], ['name' => 'ASC']);
        $campusForms = [];

        // Formulaire d'ajout
        $formNew = $this->createForm(CampusType::class, new Campus(), ['editable' => true,]);

        // Formulaires d'édition
        foreach ($allCampus as $campus) {
            $campusForms[$campus->getId()] = $this->createForm(CampusType::class, $campus)->createView();
        }

        return $this->render('admin/campus.html.twig', [
            'campusForms' => $campusForms,
            'newCampusForm' => $formNew->createView(),
            'campuses' => $allCampus,
        ]);
    }

    #[Route('/campus/new', name: 'campus_new', methods: ['GET','POST'])]
    public function newCampus(Request $request, EntityManagerInterface $em): Response
    {
        $newCampus = new Campus();
        $form = $this->createForm(CampusType::class, $newCampus, [
            'method' => 'POST',
            'editable' => true,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->addFlash("success", "Le campus a bien été ajouté !");
            $em->persist($newCampus);
            $em->flush();
        }

        return $this->redirectToRoute('campus');
    }

    #[Route('/campus/edit/{id}', name: 'campus_edit', methods: ['GET','POST'])]
    public function editCampus(Request $request, Campus $campus, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(CampusType::class, $campus);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->addFlash("success", "Le campus a bien été modifié !");
            $em->flush();
        }

        return $this->redirectToRoute('campus');
    }


    #[Route('/campus/delete/{id}', name: 'campus_delete', methods: ['GET','POST'])]
    public function deleteCampus(Request $request, Campus $campus, EntityManagerInterface $em): Response
    {

        $em->remove($campus);
        $em->flush();

        $this->addFlash("success", "Le campus a bien été supprimé !");

        return $this->redirectToRoute('villes');
    }

}
