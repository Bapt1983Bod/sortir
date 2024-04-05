<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Form\LieuType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class LieuController extends AbstractController
{
    #[Route('/lieu', name: 'app_lieu_create')]
    public function index(Request $request, EntityManagerInterface $em ): Response
    {
        $lieu = new Lieu();
        $form = $this->createForm(LieuType::class, $lieu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($lieu);
            $em->flush();
            return $this->redirectToRoute('app_creation');
        }
        if($form->isSubmitted() && !$form->isValid())
        {
            $this->addFlash('info', "Lieu non crée une erreur c'est produite");
            return $this->render('lieu/index.html.twig', [
                'form' => $form,
            ]);
        }
        return $this->render('lieu/index.html.twig', [
            'form' => $form,
        ]);
    }
}
