<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Entity\Site;
use App\Form\ProfileType;
use App\Form\SiteType;
use App\Repository\ParticipantRepository;
use App\Repository\SiteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin',name: 'app_admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    #[Route('/site', name: '_site')]
    public function adminSites (SiteRepository $siteRepository): Response
    {
        $sites = $siteRepository->findAll();

        return $this->render('admin/adminSites.html.twig', [
            'sites' => $sites,
        ]);
    }

    #[Route('/site/delete/{id}', name: '_site_delete')]
    public function deleteSite(Site $site, EntityManagerInterface $em) : Response
    {
        $em->remove($site);
        $em->flush();

        $this->addFlash('success', 'Suppression de '.$site->getNom().' réalisée !');

        return $this->redirectToRoute('app_admin_site');
    }

    #[Route('/site/update/{id}', name: '_site_update')]
    public function updateSite(?Site $site, EntityManagerInterface $em, Request $request) : Response
    {
        if (!$site) {
            $site = new Site();
        }

        $formSite = $this->createForm(SiteType::class,$site);
        $formSite->handleRequest($request);

        if ($formSite->isSubmitted() and $formSite->isValid()){
            $em->persist($site);
            $em->flush();

            $this->addFlash('success', 'Site '. $site->getNom().' ajouté ou modifié !');

            return $this->redirectToRoute('app_admin_site');
        }

        return $this->render('admin/adminSitesUpdate.html.twig',[
            'formSite'=>$formSite
        ]);
    }

    #[Route('/utilisateurs', name: '_utilisateurs')]
    public function adminUtilisateurs (ParticipantRepository $participantRepository) : Response
    {
        $Participants = $participantRepository->findAll();

        return $this->render('admin/adminUtilisateurs.html.twig',[
            "users"=>$Participants
        ]);
    }

    #[Route('/utilisateurs/delete/{id}', name: '_utilisateurs/delete')]
    public function deleteUtilisateur(Participant $participant, EntityManagerInterface $em)
    {
        $em->remove($participant);
        $em->flush();

        $this->addFlash('success', "L'utilisateur ".$participant->getPrenom()." ".$participant->getNom()." a été supprimé !");

        return $this->redirectToRoute('app_admin_utilisateurs');
    }

    #[Route('/utilisateurs/update/{id}', name: '_utilisateurs/update')]
    public function updateUtilisateur(?Participant $participant, EntityManagerInterface $em, Request $request)
    {
        if (!$participant){
            $participant = new Participant();
        }

        $formUser = $this->createForm(ProfileType::class, $participant);
        $formUser->handleRequest($request);

        if ($formUser->isSubmitted() and $formUser->isValid()){
            $em->persist($participant);
            $em->flush();

            $this->addFlash('success', 'Utilisateur ' . $participant->getPrenom() . " " . $participant->getNom() . " ajouté ou modifié !");

            return $this->redirectToRoute('app_admin_utilisateurs');
        }

        return $this->render('admin/adminUtilisateursUpdate.html.twig', [
            'form'=>$formUser,
            'user'=>$participant
        ]);
    }
}
