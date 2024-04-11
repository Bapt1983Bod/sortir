<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Sortie;
use App\Form\CreationSortieType;
use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SortieController extends AbstractController
{
    #[Route('/sortie', name: 'app_creation')]
    public function creation(Request $request, EntityManagerInterface $em ): Response
    {
        $sortie = new Sortie();
        $form = $this->createForm(CreationSortieType::class, $sortie);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $sortie->setOrganisateur($this->getUser());
            $sortie->setEtat($em->getRepository(Etat::class)->find(1));
            $sortie = $form->getData();
            $em->persist($sortie);
            $em->flush();
            return $this->redirectToRoute('app_sortie_detail', ['id' => $sortie->getId()]);
        }
        return $this->render('sortie/index.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/sortieInscription/{id}', name: 'app_sortie_inscription')]
    public function sorties(Sortie $sortie, EntityManagerInterface $em): Response
    {
        if ($sortie->getDateLimiteInscription() > new \DateTime('now')
            && $sortie->getNbInscriptionsmax() > $sortie->getParticipants()->count()
            && !$sortie->getParticipants()->contains($this->getUser()))
        {
            $participant = $this->getUser();
            $sortie->getParticipants()->add($participant);
            $em->persist($sortie);
            $em->flush();
            $participant->getSorties()-> add($sortie);
            $em->persist($participant);
            $em->flush();
            $this->addFlash('info', 'Inscription effectuée');
            return $this->redirectToRoute('app_main');
        }
        $this->addFlash('info', 'Inscription impossible - un problème est survenu');
        return $this->redirectToRoute('app_main');
    }

    #[Route('/sortieAnnulation/{id}', name: 'app_sortie_annulation')]
    public function annulation(Sortie $sortie, EntityManagerInterface $em): Response
    {
        $participant = $this->getUser();
        $sortie->getParticipants()->removeElement($participant);
        $em->persist($sortie);
        $em->flush();
        $participant->getSorties()->removeElement($sortie);
        $em->persist($participant);
        $em->flush();
        $this->addFlash('info', 'Inscription annulée');
        return $this->redirectToRoute('app_main');
    }

    #[Route('/sortie/{id}', name: 'app_sortie_detail')]
    public function sortie(int $id, SortieRepository $sortieRepository): Response
    {
        $sortie = $sortieRepository->find($id);
        return $this->render('sortie/sortie.html.twig', [
            'sortie' => $sortie
        ]);
    }

    #[Route('/sortie/publish/{id}', name: 'app_sortie_publish')]
    public function publish(Sortie $sortie, EntityManagerInterface $em): Response
    {
        $sortie->setEtat($em->getRepository(Etat::class)->find(2));
        $em->persist($sortie);
        $em->flush();
        return $this->redirectToRoute('app_profil');
    }

    #[Route('/sortie/cancel/{id}', name: 'app_sortie_stop')]
    public function stop(Sortie $sortie, EntityManagerInterface $em, EtatRepository $etatRepository): Response
    {
        //$sortie->setEtat($em->getRepository(Etat::class)->find(6));
        $sortie->setEtat($etatRepository->find(6));
        $em->persist($sortie);
        $em->flush();
        return $this->redirectToRoute('app_main');
    }

    #[Route('/sortie/edit/{id}', name: 'app_sortie_edit')]
    public function edit(Sortie $sortie, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(CreationSortieType::class, $sortie);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $sortie = $form->getData();
            $em->persist($sortie);
            $em->flush();
            return $this->redirectToRoute('app_sortie_detail', ['id' => $sortie->getId()]);
        }
        return $this->render('sortie/update.html.twig', [
            'sortie' => $sortie,
            'form' => $form
        ]);
    }

    #[Route('/sortie/delete/{id}', name: 'app_sortie_suppr')]
    public function delete(Sortie $sortie, EntityManagerInterface $em): Response
    {
        $em->remove($sortie);
        $em->flush();
        return $this->redirectToRoute('app_main');
    }

}

