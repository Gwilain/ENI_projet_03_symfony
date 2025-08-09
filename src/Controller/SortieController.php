<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Sortie;
use App\Form\CancelationType;
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

        $modif = false;

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
            "modif"=>$modif
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

        $modif = true;

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

            $this->addFlash('success', "Sortie " . ($action === 'publish' ? "publiée" : "enregistrée") . " avec succès.");

            return $this->redirectToRoute('sortie_detail', ['id' => $sortie->getId()]);
        }

        return $this->render('sortie/create.html.twig', [
            "formSorti"=>$form,
            "modif"=>$modif
        ]);
    }


    #[Route('/publier/{id}', name: 'sortie_publish', requirements: ['id'=>'\d+'], methods: ['GET', 'POST'])]
    public function publish(Sortie $sortie,  EntityManagerInterface $em, EtatRepository $etatRepo){

        $this->denyAccessUnlessGranted('SORTIE_EDIT', $sortie);

        $etatOuvert = $etatRepo->findOneBy(['code' => Etat::CODE_OUVERTE]);
        $sortie->setEtat( $etatOuvert );
        $em->persist($sortie);
        $em->flush();

        return $this->redirectToRoute('sortie_detail', ['id' => $sortie->getId()]);
    }

    #[Route('/supprimer/{id}', name: 'sortie_supress', requirements: ['id'=>'\d+'], methods: ['GET', 'POST'])]
    public function suppress(Sortie $sortie,  EntityManagerInterface $em){

        $this->denyAccessUnlessGranted('SORTIE_EDIT', $sortie);

        $em->remove($sortie);
        $em->flush();

        $this->addFlash('success', "La sortie a bien été supprimée");

        return $this->redirectToRoute('home');
    }

    #[Route('/enroll/{id}', name: 'sortie_enroll', requirements: ['id'=>'\d+'], methods: ['GET', 'POST'])]
    public function enroll(Sortie $sortie,  EntityManagerInterface $em, EtatRepository $etatRepo ){

        $this->denyAccessUnlessGranted('SORTIE_ENROLL', $sortie);

        $sortie->addParticipant($this->getUser());

        //ON change l'état si c'est plein
        if($sortie->getParticipants()->count() == $sortie->getNbInscriptionMax() ){
            $sortie->setEtat($etatRepo->findOneBy(['code' => Etat::CODE_CLOTUREE]));
        }

        $em->persist($sortie);
        $em->flush();

        $this->addFlash('success', "Vous êtes bien inscrit à cette sortie !!!");

        return $this->redirectToRoute('sortie_detail', ['id' => $sortie->getId()]);
    }

    #[Route('/withdraw/{id}', name: 'sortie_withdraw', requirements: ['id'=>'\d+'], methods: ['GET', 'POST'])]
    public function withdraw(Sortie $sortie,  EntityManagerInterface $em, EtatRepository $etatRepo){

        $this->denyAccessUnlessGranted('SORTIE_WITHDRAW', $sortie);

        //ON change l'état si c'est plus plein
        if($sortie->getParticipants()->count() == $sortie->getNbInscriptionMax() ){
            $sortie->setEtat($etatRepo->findOneBy(['code' => Etat::CODE_OUVERTE]));
        }

        $sortie->removeParticipant($this->getUser());


        $em->persist($sortie);
        $em->flush();

        $this->addFlash('success', "Vous n'êtes plus inscrit à cette sortie");

        return $this->redirectToRoute('sortie_detail', ['id' => $sortie->getId()]);
    }

    #[Route('/cancel/{id}', name: 'sortie_cancel', requirements: ['id'=>'\d+'], methods: ['GET', 'POST'])]
    public function cancel(Sortie $sortie, Request $request, EntityManagerInterface $em, EtatRepository $etatRepo){

        $this->denyAccessUnlessGranted('SORTIE_CANCELABLE', $sortie);;

        $form = $this->createForm(CancelationType::class, $sortie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $etat = $etatRepo->findOneBy(['code' => Etat::CODE_ANNULEE]);
            $sortie->setEtat($etat);


            $em->persist($sortie);
            $em->flush();
            $this->addFlash('success', "La sortie a bien été annulée");

            return $this->redirectToRoute('sortie_detail', ['id' => $sortie->getId()]);
        }

        return $this->render('sortie/cancel.html.twig', [
            "formSorti"=>$form,
            "sortie"=>$sortie,
        ]);
    }




    /***********************************************************************/
    //MINI API POUR AVOIR L'ADRESSE DU LIEU DYNAMIQUEMENT'

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
