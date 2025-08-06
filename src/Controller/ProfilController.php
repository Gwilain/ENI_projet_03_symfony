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
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/profile')]
final class ProfilController extends AbstractController
{


    #[Route('/{id}', name: 'profil', methods: ['GET'], requirements: ['id'=>'\d+'])]
    public function show(User $user): Response{
        //$user = $this->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('profil/show.html.twig', [
            'user' => $user,
        ]);

    }


    #[Route('/modifier', name: 'profil_edit', methods: ['GET', 'POST'])]
    public function profile(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher,

    ): Response {

        $user = $this->getUser();
        $userForm = $this->createForm(UserType::class, $user, ["validation_groups" => ["edit"]]);
        $userForm->handleRequest($request);

        if ($userForm->isSubmitted() && $userForm->isValid()) {
//            dd($userForm);

                $plainPassword = $userForm->get('plainPassword')->getData();
                if ($plainPassword) {
                    $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
                    $user->setPassword($hashedPassword);
                }
                $em->persist($user);
                $em->flush();
                $this->addFlash("success", "Votre profil a bien été modifié.");

                return $this->redirectToRoute('profil', ['id' => $user->getId()]);

        }elseif ($userForm->isSubmitted()) {

            $this->addFlash('error', 'Encore quelques petites erreurs à corriger !');

        }

        return $this->render('profil/edit.html.twig', [
            "user" => $user,
            "userForm" => $userForm,
        ]);
    }



/*    #[IsGranted('ROLE_USER')]
    #[Route('/modifier', name: 'profil_edit', methods: ['GET', 'POST'])]
    public function profile(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher,
        ValidatorInterface $validator
    ): Response {
        $user = $this->getUser();
        $userForm = $this->createForm(UserType::class, $user, ["validation_groups" => ["edit"]]);
        $userForm->handleRequest($request);

        if ($userForm->isSubmitted()) {
//            dd($userForm);

            if ($userForm->isValid()) {
                $plainPassword = $userForm->get('plainPassword')->getData();
                if ($plainPassword) {
                    $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
                    $user->setPassword($hashedPassword);
                }

                try {
                    $em->flush();
                    $this->addFlash("success", "Votre profil a bien été modifié.");
                    return $this->redirectToRoute('profil', ['id' => $user->getId()]);
                } catch (\Doctrine\DBAL\Exception\UniqueConstraintViolationException $e) {
                    $this->addFlash('error', 'Un élément doit être unique (email ou pseudo) est déjà utilisé.');
                }
            }else{


                $this->addFlash('error', 'Un élément doit être unique (email ou pseudo) est déjà utilisé.');
            }
        }


        return $this->render('profil/edit.html.twig', [
            "user" => $user,
            "userForm" => $userForm->createView(),
        ]);
    }*/
}