<?php

namespace App\Services;

use App\Entity\Participant;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;

class AdminUtilisateurService
{
    private $entityManager;
    private $participantRepository;

    /**
     * @param $EntityManager
     * @param $ParticipantRepository
     */
    public function __construct(EntityManagerInterface $EntityManager,ParticipantRepository  $ParticipantRepository)
    {
        $this->entityManager = $EntityManager;
        $this->participantRepository = $ParticipantRepository;
    }

    public function setActif(Participant $participant) : void
    {
        if($participant->isActif()){
            $participant->setActif(false);
        } else {
            $participant->setActif(true);
        }
        $this->entityManager->persist($participant);
        $this->entityManager->flush();
    }

    public function setRole(Participant $participant) : void
    {
        if ($participant->isAdministrateur()) {
            $participant->setAdministrateur(false);
            $participant->setRoles(["ROLE_USER"]);
        } else {
            $participant->setAdministrateur(true);
            $participant->setRoles(["ROLE_ADMIN"]);
        }
        $this->entityManager->persist($participant);
        $this->entityManager->flush();
    }

}