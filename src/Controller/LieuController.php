<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Form\LieuType;
use App\Services\CallApiAdresse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class LieuController extends AbstractController
{

    #[Route('/lieu', name: 'app_lieu_create')]
    public function index(CallApiAdresse $callApiAdresse, Request $request, EntityManagerInterface $em ): Response
    {


        $lieu = new Lieu();
        $form = $this->createForm(LieuType::class, $lieu);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $coordonate = $callApiAdresse->getData($form->getData()->getRue(), $form->getData()->getVille()->getNom())['features'][0]['geometry']['coordinates'];
            $adresseComplete = $callApiAdresse->getData($form->getData()->getRue(), $form->getData()->getVille()->getNom())['features'][0]['properties']['label'];
            $lieu->setLongitude($coordonate[0]);
            $lieu->setLatitude($coordonate[1]);

            $this->addFlash('success', 'Lieu crée avec success '.$adresseComplete.'');
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
