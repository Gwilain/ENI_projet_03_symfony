<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/profile')]
final class ProfilController extends AbstractController
{

    /*#[Route('/modifier', name: 'profil_edit', methods: ['GET',"POST"])]
    public function profile(Request $request, EntityManagerInterface $em ): Response
    {
        $user = $this->getUser();

        $userForm = $this->createForm(UserType::class, $user);

        if($userForm->handleRequest($request)->isSubmitted()){

            $em->persist($user);
            $em->flush();
            $this->addFlash("success", "Votre profile a bien été modifié");

            return $this->redirectToRoute('home', []);
        }

         return $this->render('profil/edit.html.twig', [
             "user" => $user,
             "userForm" => $userForm->createView(),
        ]);
    }*/

    #[Route('/modifier', name: 'profil_edit', methods: ['GET', 'POST'])]
    public function profile(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        $user = $this->getUser();
        $userForm = $this->createForm(UserType::class, $user);
        $userForm->handleRequest($request);

        if ($userForm->isSubmitted() && $userForm->isValid()) {

            //Récupération du mot de passe en clair (saisi dans le form)
            $plainPassword = $userForm->get('plainPassword')->getData();

            if ($plainPassword) {
                // Hash du nouveau mot de passe, puis on le set dans l'entité
                $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
                $user->setPassword($hashedPassword);
            }

            $em->flush();

            $this->addFlash("success", "Votre profil a bien été modifié.");
            return $this->redirectToRoute('home');
        }

        return $this->render('profil/edit.html.twig', [
            "user" => $user,
            "userForm" => $userForm->createView(),
        ]);
    }
}
