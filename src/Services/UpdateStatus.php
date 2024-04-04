<?php

namespace App\Services;

use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;

class UpdateStatus
{

    public function updateStatus(EntityManagerInterface $em,SortieRepository $sortieRepository, EtatRepository $etatRepository,):void
    {
        $today = ( new \DateTime('now') )->format('Y-m-d');
        $old =  ( new \DateTime('-1 month') )->format('Y-m-d');

        $sortiesOuverte = $sortieRepository->findByEtat($etatRepository->find(2));
        foreach ($sortiesOuverte as $sortie) {
            if ($sortie->getDateLimiteInscription()->format('Y-m-d') < $today) {
                $sortie->setEtat($etatRepository->find(3));
                $em->persist($sortie);
                $em->flush();
            }
        }
        $sortiesClos = $sortieRepository->findByEtat($etatRepository->find(3));
        foreach ($sortiesClos as $sortie) {
            if ($sortie->getDateHeureDebut()->format('Y-m-d') ==  $today) {
                $sortie->setEtat($etatRepository->find(4));
                $em->persist($sortie);
                $em->flush();
            }
        }
            $sortiesClos = $sortieRepository->findByEtat($etatRepository->find(3));
            foreach ($sortiesClos as $sortie) {
                if ($sortie->getDateHeureDebut()->format('Y-m-d') <=  $today) {
                    $sortie->setEtat($etatRepository->find(5));
                    $em->persist($sortie);
                    $em->flush();
                }
        }
        $sortiesEnCours = $sortieRepository->findByEtat($etatRepository->find(4));
        foreach ($sortiesEnCours as $sortie) {
            if ($sortie->getDateHeureDebut()->format('Y-m-d') <=  $today) {
                $sortie->setEtat($etatRepository->find(5));
                $em->persist($sortie);
                $em->flush();
            }
        }
        $sortiesPassees = $sortieRepository->findByEtat($etatRepository->find(5));
        foreach ($sortiesPassees as $sortie) {
            if ($sortie->getDateHeureDebut()->format('Y-m-d') <=  $old) {
                $sortie->setEtat($etatRepository->find(7));
                $em->persist($sortie);
                $em->flush();
            }
        }
        $sortiesAnnulees = $sortieRepository->findByEtat($etatRepository->find(6));
        foreach ($sortiesAnnulees as $sortie) {
            if ($sortie->getDateHeureDebut()->format('Y-m-d') <=  $old) {
                $sortie->setEtat($etatRepository->find(7));
                $em->persist($sortie);
                $em->flush();
            }
        }
    }

}