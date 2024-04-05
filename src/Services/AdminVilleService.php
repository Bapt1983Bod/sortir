<?php

namespace App\Services;

use App\Entity\Ville;
use App\Form\VilleType;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class AdminVilleService
{
    private $villeRepository;
    private $entityManager;
    private $formFactory;

    /**
     * @param $villeRepository
     * @param $entityManager
     * @param $formInterface
     */
    public function __construct(VilleRepository $villeRepository,EntityManagerInterface $entityManager,FormFactoryInterface $formFactory)
    {
        $this->villeRepository = $villeRepository;
        $this->entityManager = $entityManager;
        $this->formFactory = $formFactory;
    }

    public function getVilleForm():FormInterface
    {
        $ville = new Ville();
        return $this->formFactory->create(VilleType::class,$ville);
    }

    public function listeVilles(Request $request)
    {
        if ($request->query->get("keyword")) {
            return $this->villeRepository->findByKeyword($request->query->get("keyword"));
        } else {
            return $this->villeRepository->findAll();
        }
    }

    public function addVille(Request $request) : void
    {
        $ville = new Ville();
        $form = $this->formFactory->create(VilleType::class,$ville);
        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {
            $this->entityManager->persist($ville);
            $this->entityManager->flush();
            return;
        }

        throw new BadRequestHttpException('Erreur ! Ajout impossible');
    }

    public function deleteVille(Request $request) : void
    {

    }
}