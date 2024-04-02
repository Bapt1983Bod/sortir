<?php

namespace App\Controller;

use App\Repository\SortieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


class MainController extends AbstractController
{
    #[Route('/main', name: 'app_main')]
    public function index(Request $request, SortieRepository $sortieRepository): Response
    {

        // Récupérer les sorties depuis la base de données
        $sorties = $sortieRepository->findAll();

        return $this->render('main/index.html.twig', [
            'sorties' => $sorties,
        ]);
    }
}
