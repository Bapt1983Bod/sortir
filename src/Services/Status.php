<?php

namespace App\Services;

use App\Repository\EtatRepository;
use Doctrine\ORM\EntityManagerInterface;

class Status
{
    private $entityManager;
    private $etatRepository;

    /**
     * @param $entityManager
     * @param $etatRepository
     */
    public function __construct(EntityManagerInterface $entityManager,EtatRepository $etatRepository)
    {
        $this->entityManager = $entityManager;
        $this->etatRepository = $etatRepository;
    }

    public function status(Array $sorties): void
    {
        $today = ( new \DateTime('now', new \DateTimeZone('Europe/Paris')) )->getTimestamp();
        $old =  ( new \DateTime('-1 month', new \DateTimeZone('Europe/Paris')) )->getTimestamp();
        $etats = $this->etatRepository->findAll();

        foreach ($sorties as $sortie){
            $dateHeureDebut = $sortie->getDateHeureDebut()->modify('- 120 minutes ');
            $dateHeureDebut = $dateHeureDebut->getTimestamp();
            $dateFinInscription = $sortie->getDateLimiteInscription()->modify('-120 minutes');
            $dateFinInscription = $dateFinInscription->getTimestamp();
            $dateHeureFinSortie = clone $sortie->getDateHeureDebut();
            $dateHeureFinSortie->modify('+' . $sortie->getDuree() . ' minutes');
            $dateHeureFinSortie = $dateHeureFinSortie->getTimestamp();

            if (($today >= $dateFinInscription) or ($sortie->getNbInscriptionsmax() <= count($sortie->getParticipants()))){
                if ($today > $dateHeureDebut){
                    if ($today > $dateHeureFinSortie){
                        if($dateHeureFinSortie<$old) {
                            $sortie->setEtat($etats[6]); // Passage en état archivé
                        } else {
                            $sortie->setEtat($etats[4]); // Passage en état passée
                        }
                    } else {
                        $sortie->setEtat($etats[3]); // Passage en état en cours
                    }
                } else {
                    $sortie->setEtat($etats[2]); // Passage en état cloturé
                }
            } else {
                $sortie->setEtat($etats[1]); // Passage en état ouverte
            }
            $this->entityManager->persist($sortie);
        }
        $this->entityManager->flush();
    }
}