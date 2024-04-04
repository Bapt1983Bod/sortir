<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\ProfileType;
use App\Form\RegistrationFormType;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

#[IsGranted('IS_AUTHENTICATED')]
class ProfilController extends AbstractController
{
    #[Route('/profil', name: 'app_profil')]
    public function myProfil(): Response
    {
        return $this->render('profil/monProfil.html.twig');
    }

    #[Route('/profil/{id}', name: 'app_profil_show')]
    public function showprofil($id, ParticipantRepository $participantRepository): Response
    {
        $participant = $participantRepository->find($id);

        return $this->render('profil/profilParticipant.html.twig', [
            "participant"=>$participant
        ]);
    }

    #[Route('/profil/update', name: 'app_profil_update')]
    public function update(EntityManagerInterface $em, Request $request, SluggerInterface $slugger): Response
    {
        // Récupération de l'utilisateur connecté
        $user=$this->getUser();

        // Création d'un formulaire contenant l'utilisateur connecté
        $form = $this ->createForm(ProfileType::class,$user);
        $form->handleRequest($request);

        // Vérification si formulaire soumis et valide
        if ($form->isSubmitted() and $form->isValid()){

            // Vérif si présence d'une photo de profil
            if ($form->get('image_file')->getData() instanceof UploadedFile){
                // suppression de la photo déjà présente
                if($user->getPhoto() && file_exists('images/profil/'.$user->getPhoto())){
                    unlink('images/profil/'.$user->getPhoto());
                }

                // On récupère l'objet
                $photo = $form->get('image_file')->getData();
                // standardisation du nom du fichier
                $fileName = $slugger->slug($user->getNom().$user->getPrenom()).uniqid().'.'.$photo->guessExtension();
                // renommage et transfert du fichier dans le dossier
                $photo->move('images/profil',$fileName);

                $user->setPhoto($fileName);
            }



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
