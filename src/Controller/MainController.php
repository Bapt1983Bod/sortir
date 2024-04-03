<?php

namespace App\Controller;

use App\Entity\Site;
use App\Repository\SortieRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;


class MainController extends AbstractController
{

    #[Route('/main', name: 'app_main')]
    public function index(Request $request, SortieRepository $sortieRepository, EntityManagerInterface $entityManager): Response
    {
        $siteId = $request->query->get('site');
        $keyword = $request->query->get('keyword');
        $startDate = $request->query->get('start_date'); // Récupérer la date de début depuis la requête
        $endDate = $request->query->get('end_date'); // Récupérer la date de fin depuis la requête
        $user = $this->getUser();

        if ($siteId) {
            $sorties = $sortieRepository->findBySite($siteId);
        } else {
            $sorties = $sortieRepository->findAll(); // Récupérer toutes les sorties si aucun site sélectionné
        }

        // Appliquer la recherche par mot-clé si un mot-clé est saisi
        if ($keyword) {
            $sorties = $sortieRepository->findByKeyword($keyword);
        }

        // Appliquer le filtre de date si les deux dates sont fournies
        if ($startDate && $endDate) {
            // Convertir les chaînes de date en objets DateTime
            $startDateTime = new DateTime($startDate);
            $endDateTime = new DateTime($endDate);

            // Récupérer les sorties entre les deux dates
            $sorties = $sortieRepository->findByDateRange($startDateTime, $endDateTime);
        }

        // Récupérer la liste des sites depuis la base de données
        $sites = $entityManager->getRepository(Site::class)->findAll();

        // Passer les sorties filtrées et la liste des sites à la vue
        return $this->render('main/index.html.twig', [
            'sorties' => $sorties,
            'sites' => $sites, // Passer la liste des sites à la vue
            'user' => $user
        ]);
    }
}
