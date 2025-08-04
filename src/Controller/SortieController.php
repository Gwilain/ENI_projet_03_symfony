<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Repository\SortieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/sortie')]
final class SortieController extends AbstractController
{
    #[Route('/{id}', name: 'sortie_detail', requirements: ['id'=>'\d+'], methods: ['GET'])]
    public function sortie(Sortie $sortie): Response
    {
        //$sortie = $repo->findAll();


        return $this->render('sortie/detail.html.twig', [
            'sortie' => $sortie,
        ]);
    }
}
