<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\ProfileType;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProfilController extends AbstractController
{
    #[Route('/profil', name: 'app_profil')]
    public function index(): Response
    {
        return $this->render('profil/index.html.twig');
    }

    #[Route('/profil/update', name: 'app_profil_update')]
    public function update(EntityManagerInterface $em, Request $request): Response
    {
        // Récupération de l'utilisateur connecté
        $user=$this->getUser();

        // Création d'un formulaire contenant l'utilisateur connecté
        $form = $this ->createForm(ProfileType::class,$user);
        $form->handleRequest($request);

        if ($form->isSubmitted()){
            $em->persist($user);
            $em->flush();

            $this->addFlash("success","Profil mis à jour avec succès !");

            return $this->redirectToRoute("app_profil");
        }

        return $this->render("profil/update.html.twig",[
            "form"=>$form,
        ]);



    }
}
