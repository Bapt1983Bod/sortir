<?php

namespace App\Services;

use App\Entity\Site;
use App\Form\SiteType;
use App\Repository\SiteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class AdminSiteService
{
    private $siteRepository;
    private $entityManager;
    private $formFactory;

    /**
     * @param $siteRepository
     * @param $entityManager
     */
    public function __construct(SiteRepository $siteRepository,EntityManagerInterface $entityManager, FormFactoryInterface $formFactory)
    {
        $this->siteRepository = $siteRepository;
        $this->entityManager = $entityManager;
        $this->formFactory = $formFactory;

    }

    public function listeSites (Request $request)
    {
        if ($request->query->get("keyword")) {
            return $this->siteRepository->findByKeyword($request->query->get("keyword"));
        } else {
            return $this->siteRepository->findAll();
        }
    }

    public function getSiteForm(): FormInterface
    {
        $newSite = new Site();
        return $this->formFactory->create(SiteType::class, $newSite);
    }

    public function addSite(Request $request): void
    {
        $newSite = new Site();
        $form = $this->formFactory-> create(SiteType::class,$newSite);
        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()){
            $this->entityManager->persist($newSite);
            $this->entityManager->flush();

            return;
        }
        throw new BadRequestHttpException('Erreur ! Ajout impossible');
    }

    public function updateSite(Request $request): void
    {
        if ($request->query->get("name")){
            $site = $this->siteRepository->find($request->query->get("id"));
            $site->setNom($request->query->get("name"));

            $this->entityManager->persist($site);
            $this->entityManager->flush();
            return;
        }
        throw new BadRequestHttpException('Erreur ! Modification impossible');
    }

    public function deleteSite(Site $site): void
    {
        try {
            $this->entityManager->remove($site);
            $this->entityManager->flush();
        } catch (\Exception $exception){
            throw new BadRequestHttpException("Suppression impossible");
        }
    }

}