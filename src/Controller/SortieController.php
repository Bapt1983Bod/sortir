<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Entity\Sortie;
use App\Form\CreationSortieType;
use Doctrine\ORM\EntityManager;
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
            $sortie = $form->getData();
            $em->persist($sortie);
            $em->flush();
            return $this->redirectToRoute('app_main');
        }
        return $this->render('sortie/index.html.twig', [
            'form' => $form
        ]);
    }



}

