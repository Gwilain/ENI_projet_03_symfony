<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;
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


//    #[Route('/modifier/{id}', name: 'profil_edit', methods: ['GET', 'POST'], requirements: ['id'=>'\d+'])]
    #[Route('/modifier/{id}', name: 'profil_edit', methods: ['GET', 'POST'], defaults: ['id' => null])]
    public function profile(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher,
        SluggerInterface $slugger,
        ?User $user = null,
    ): Response {
        $isNew = false;

        if (!$user) {
            // Création d'un nouvel utilisateur
            $this->denyAccessUnlessGranted('ROLE_ADMIN');
            $user = new User();
            $user->setEventAdmin(true);
            $isNew = true;
        } else {
            // Edition d'un utilisateur existant
            $this->denyAccessUnlessGranted('USER_EDIT', $user);
        }


        $userForm = $this->createForm(UserType::class, $user, [
            'is_new' => $isNew,
            "validation_groups" =>  $isNew ? ['create'] : ['edit'],
            "is_admin" => $this->isGranted('ROLE_ADMIN') && $user != $this->getUser(),]);
        $userForm->handleRequest($request);

        if ($userForm->isSubmitted() && $userForm->isValid()) {

                $plainPassword = $userForm->get('plainPassword')->getData();
                if ($plainPassword) {
                    $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
                    $user->setPassword($hashedPassword);
                }

                $removePhoto = $userForm->get('removePhoto')->getData();
                $profilePictureFile = $userForm->get('profilePicture')->getData();

                if ($removePhoto) {

                    if ($user->getImageFile()) {
                        unlink($this->getParameter('profile_pictures_directory').'/'.$user->getImageFile());
                        $user->setImageFile(null);
                    }
                }

                if ($profilePictureFile) {
                    $originalFilename = pathinfo($profilePictureFile->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename . '-' . uniqid() . '.' . $profilePictureFile->guessExtension();

                    try {
                        $profilePictureFile->move(
                            $this->getParameter('profile_pictures_directory'),
                            $newFilename
                        );
                    } catch (FileException $e) {
//                        throw new \Exception("Erreur lors de l'enregistrement de l'image");
                    }

                    $user->setImageFile($newFilename);
                }

                $em->persist($user);
                $em->flush();

            $message = $isNew ? "L'utilisateur a bien été créé." : "Le profil a bien été modifié.";
            $this->addFlash("success", $message);

            return $this->redirectToRoute('profil', ['id' => $user->getId()]);

        }elseif ($userForm->isSubmitted()) {

            $this->addFlash('error', 'Encore quelques petites erreurs à corriger !');

        }

        return $this->render('profil/edit.html.twig', [
            "user" => $user,
            "userForm" => $userForm,
            "isNew" => $isNew,
        ]);
    }


}