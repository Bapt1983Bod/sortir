<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Site;
use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use App\Services\Status;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;


class MainController extends AbstractController
{
    #[Route('/main', name: 'app_main')]
    public function index(Status $status, Request $request, SortieRepository $sortieRepository, EntityManagerInterface $entityManager): Response
    {

        // Récupérer les paramètres de la requête
        $siteId = $request->query->get('site');
        $keyword = $request->query->get('keyword');
        $startDate = $request->query->get('start_date');
        $endDate = $request->query->get('end_date');
        $organisateur = $request->query->get('organisateur');
        $participantRegistered = $request->query->get('participant_registered');
        $participantNotRegistered = $request->query->get('participant_not_registered');
        $participant = $this->getUser();
        $sortiePassee = $request->query->get('sortie_passee');

        // Commencer avec toutes les sorties
        $sorties = $sortieRepository->findAllOptimised();

        $status -> status($sorties);

       //  Appliquer les filtres un par un
        if ($siteId) {
            $sorties = $sortieRepository->findBySite($siteId);
        }
        if ($sortiePassee) {
            $etatPassee = $entityManager->getRepository(Etat::class)->find(5); // ID de l'état "Passée"
            $sorties = $sortieRepository->findBy(['etat' => $etatPassee]);
        }
        if ($keyword) {
            $sorties = $sortieRepository->findByKeyword($keyword);
        }
        if ($startDate && $endDate) {
            $startDateTime = new DateTime($startDate);
            $endDateTime = new DateTime($endDate);
            $sorties = $sortieRepository->findByDateRange($startDateTime, $endDateTime);
        }
        if ($organisateur) {
            $sorties = $sortieRepository->findByOrganisateur($this->getUser());
        }
        // Filtrer les sorties selon les cas cochés
        if ($participantRegistered && $participantNotRegistered) {
            $sorties = $sortieRepository->findRegisteredAndNotRegisteredByParticipant($participant);
        } elseif ($participantRegistered) {
            $sorties = $sortieRepository->findByParticipant($participant);
        } elseif ($participantNotRegistered) {
            $sorties = $sortieRepository->findNotRegisteredByParticipant($participant);
        }
        // Récupérer la liste des sites depuis la base de données
        $sites = $entityManager->getRepository(Site::class)->findAll();
        // Passer les sorties filtrées et la liste des sites à la vue
        return $this->render('main/index.html.twig', [
            'sorties' => $sorties,
            'sites' => $sites,
            'user' => $participant
        ]);
    }

}