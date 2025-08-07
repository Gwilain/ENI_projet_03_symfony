<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Sortie;
use App\Form\SortieType;
use App\Repository\EtatRepository;
use App\Repository\LieuRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/sortie')]
final class SortieController extends AbstractController
{
    #[Route('/{id}', name: 'sortie_detail', requirements: ['id'=>'\d+'], methods: ['GET'])]
    public function sortie(Sortie $sortie): Response
    {
        $this->denyAccessUnlessGranted('SORTIE_VIEW', $sortie);

        return $this->render('sortie/detail.html.twig', [
            'sortie' => $sortie,
        ]);
    }


    #[Route('/creer', name: 'sortie_create', methods: ['GET', 'POST'])]
    public function createSortie(Request $request, EntityManagerInterface $em, EtatRepository $etatRepo): Response
    {
        $sortie = new Sortie();

        $user = $this->getUser();
        $sortie->setOrganisateur( $user );
        $sortie->setCampus( $user->getCampus() );

        $defaultState = $etatRepo->findOneBy(['code' => Etat::CODE_EN_CREATION]);
        $sortie->setEtat($defaultState);

        $form = $this->createForm(SortieType::class, $sortie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $action = $request->request->get('action');
            if ($action === 'publish') {
                $state = $etatRepo->findOneBy(['code' => Etat::CODE_OUVERTE]);
                $sortie->setEtat($state);
            }


            $em->persist($sortie);
            $em->flush();
//            $this->addFlash("success", "Votre Sortie a bien été créée.");
            $this->addFlash('success', "Sortie " . ($action === 'publish' ? "publiée" : "enregistrée") . " avec succès.");

            return $this->redirectToRoute('sortie_detail', ['id' => $sortie->getId()]);
        }

        return $this->render('sortie/create.html.twig', [
            "formSorti"=>$form,
        ]);
    }


    #[Route('/modifier/{id}', name: 'sortie_edit', requirements: ['id'=>'\d+'], methods: ['GET', 'POST'])]
    public function editSorti( Request $request,
                               Sortie $sortie,
                               EntityManagerInterface $em,
                               EtatRepository $etatRepo
    ): Response {

        //utilisation du Voter SortieVoter
        $this->denyAccessUnlessGranted('SORTIE_EDIT', $sortie);

        $form = $this->createForm(SortieType::class, $sortie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $action = $request->request->get('action');
            if ($action === 'publish') {
                $state = $etatRepo->findOneBy(['code' => Etat::CODE_OUVERTE]);
                $sortie->setEtat($state);
            }

            $em->persist($sortie);
            $em->flush();
//            $this->addFlash("success", "Votre Sortie a bien été créée.");
            $this->addFlash('success', "Sortie " . ($action === 'publish' ? "publiée" : "enregistrée") . " avec succès.");

            return $this->redirectToRoute('sortie_detail', ['id' => $sortie->getId()]);
        }

        return $this->render('sortie/create.html.twig', [
            "formSorti"=>$form,
        ]);


    }



    #[Route('/lieu/adresse/{id}', name: 'sortie-adress', methods: ['GET'])]
    public function getAdresse(int $id, LieuRepository $lieuRepository): JsonResponse
    {
        $lieu = $lieuRepository->find($id);
        if (!$lieu) {
            return new JsonResponse(['error' => 'Lieu non trouvé'], 404);
        }

        return new JsonResponse([
            'adresse' => $lieu->getRue(),
            'code_postal' => $lieu->getVille()->getCodePostal(),
            'lat' => $lieu->getLatitude(),
            'long' => $lieu->getLongitude(),
        ]);
    }

}
