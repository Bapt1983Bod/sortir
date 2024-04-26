<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Entity\Ville;
use App\Repository\VilleRepository;
use App\Services\ApiAdresse;
use App\Services\CallApiAdresse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

class LieuController extends AbstractController
{
    #[Route('/lieu', name: 'app_lieu_create')]
    public function index(CallApiAdresse $callApiAdresse, Request $request, EntityManagerInterface $em,VilleRepository $villeRepository ,ApiAdresse $apiAdresse, SessionInterface $session ): Response
    {
//        $lieu = new Lieu();
//        $form = $this->createForm(LieuType::class, $lieu);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//
//            $coordonate = $callApiAdresse->getData($form->getData()->getRue(), $form->getData()->getVille()->getNom())['features'][0]['geometry']['coordinates'];
//            $adresseComplete = $callApiAdresse->getData($form->getData()->getRue(), $form->getData()->getVille()->getNom())['features'][0]['properties']['label'];
//            $lieu->setLongitude($coordonate[0]);
//            $lieu->setLatitude($coordonate[1]);
//
//            $this->addFlash('success', 'Lieu crée avec success '.$adresseComplete.'');
//            $em->persist($lieu);
//            $em->flush();
//            return $this->redirectToRoute('app_creation');
//        }
//        if($form->isSubmitted() && !$form->isValid())
//        {
//            $this->addFlash('info', "Lieu non crée une erreur c'est produite");
//            return $this->render('lieu/index.html.twig', [
//                'form' => $form,
//            ]);
//        }
//        return $this->render('lieu/index.html.twig', [
//            'form' => $form,
//        ]);

// Autre version

        $adresses = [];

        $rue = trim($request->query->get('rue'));
        $rue = str_ireplace(' ','+',$rue);
        $rue = str_ireplace('-','+', $rue);
        $codePostal = trim($request->query->get('codepostal'));

        $nomLieu = $request->query->get('nomLieu');
        $idAdresse = $request->query->get('idAdresse');

        // si un lieu et un id adresse sont presents
        if ($nomLieu and $idAdresse) {
            // on récupère les adresses de la session
            $adresses = $session->get('adresses');
            // on recherche l'adresse correspondante à l'id
            foreach ($adresses as $ad) {
                if($idAdresse == $ad['id']){
                    $adresse = $ad;
                }
            }

            // on recherche la ville correspondante à l'adresse
            // si la ville n'existe pas, on l'ajoute
            if (!$villeRepository->findByNameCp($adresse['city'],$adresse['postcode'])) {
                $ville = new Ville();
                $ville->setNom($adresse['city']);
                $ville->setCodePostal($adresse['postcode']);

                $em->persist($ville);
                $em->flush();
            } else {
                // sinon on la récupère
                $ville = $villeRepository->findByNameCp($adresse['city'],$adresse['postcode']);
            }

            // on ajoute le lieu
            $lieu = new Lieu();
            $lieu->setNom($nomLieu)
                -> setRue($adresse['name'])
                -> setLatitude($adresse['lat'])
                -> setLongitude($adresse['long'])
                -> setVille($ville);
            $em->persist($lieu);
            $em->flush();

            $this->addFlash('success', 'lieu '.$lieu->getNom().' ajouté !');

            return $this->redirectToRoute('app_creation');
        }

        if($rue and $codePostal) {

            $stringRegex = '/^[a-zA-ZÀ-ÿ0-9\s\-+]+$/';
            $intRegex = '/^\d{5}$/';

            // verif si regex ok
            if (preg_match($stringRegex,$rue) == 1 && preg_match($intRegex,$codePostal) == 1) {
                // Récupération des adresses via le service
                $adresses = $apiAdresse->getDatas($rue,$codePostal);
                // transmission des adresses dans la session
                $session->set('adresses', $adresses);
            } else {
                // message d'erreur
                $this->addFlash('danger',"format rue ou code postal incorrect");
            }
        } else {
            // suppression de la session
            $session->remove('adresses');
        }

        return $this->render('lieu/lieu.html.twig',[
            'adresses'=>$adresses
        ]);


    }
}
