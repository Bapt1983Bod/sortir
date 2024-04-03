<?php

namespace App\Controller;

use App\Entity\Site;
use App\Repository\SiteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin',name: 'app_admin')]
class AdminController extends AbstractController
{
    #[Route('/site', name: '_site')]
    public function index(SiteRepository $siteRepository): Response
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
    public function updateSite(Site $site)
    {
        dd($site);

    }
}
