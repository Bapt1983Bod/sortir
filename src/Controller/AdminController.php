<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Entity\Site;
use App\Entity\Ville;
use App\Form\ProfileType;
use App\Form\SiteType;
use App\Form\VilleType;
use App\Repository\ParticipantRepository;
use App\Repository\SiteRepository;
use App\Repository\VilleRepository;
use App\Services\HashPassword;
use App\Services\PhotoUploader;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin',name: 'app_admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{

// ADMINISTRATION DES SITES

    // affichage liste des sites et ajout/màj de site
    #[Route('/site', name: '_site')]
    public function adminSites (SiteRepository $siteRepository, Request $request, EntityManagerInterface $em): Response
    {

        if ($request->query->get('keyword')){
            $sites = $siteRepository ->findByKeyword($request->query->get('keyword'));
        } else {
            $sites = $siteRepository->findAll();
        }

        // Ajout d'un site

        $newSite = new Site();
        $form = $this->createForm(SiteType::class,$newSite);
        $form->handleRequest($request);

        if($form->isSubmitted() and  $form->isValid()){
            $em->persist($newSite);
            $em->flush();

            $this->addFlash('success',"La ville ".$newSite->getNom()." a été ajoutée !");

            return $this->redirectToRoute('app_admin_site');
        }

        // Modification du site

        if ($request->query->get("name")){
            $site = $siteRepository->find($request->query->get('id'));
            $site->setNom($request->query->get("name"));

            $em->persist($site);
            $em->flush();

            $this->addFlash('success',"La ville ".$site->getNom()." a été modifiée !");

            return $this->redirectToRoute('app_admin_site');
        }

        return $this->render('admin/adminSites.html.twig', [
            'sites' => $sites,
            'form' => $form->createView(),
        ]);
    }

    // Suppression d'un site
    #[Route('/site/delete/{id}', name: '_site_delete')]
    public function deleteSite(Site $site, EntityManagerInterface $em) : Response
    {
        try{
            $em->remove($site);
            $em->flush();

            $this->addFlash('success', 'Suppression de '.$site->getNom().' réalisée !');
        } catch (ForeignKeyConstraintViolationException $exception){
            $this->addFlash("danger", "Impossible de supprimer le site ".$site->getNom()." car il est lié à d'autres éléments (Participants).");
        } catch (\Exception $exception) {
            $this->addFlash("danger", "Une erreur s'est produite lors de la suppression de la ville ".$site->getNom()." : ".$exception->getMessage());
        }

        return $this->redirectToRoute('app_admin_site');
    }


// ADMINISTRATION DES UTILISATEURS

    #[Route('/utilisateurs', name: '_utilisateurs')]
    public function adminUtilisateurs (ParticipantRepository $participantRepository) : Response
    {
        $Participants = $participantRepository->findAll();

        return $this->render('admin/adminUtilisateurs.html.twig',[
            "users"=>$Participants
        ]);
    }

    // Suppression d'un utilisateur
    #[Route('/utilisateurs/delete/{id}', name: '_utilisateurs/delete')]
    public function deleteUtilisateur(Participant $participant, EntityManagerInterface $em, PhotoUploader $photoUploader) : Response
    {
        // suppression de la photo du participant
        $photoUploader->deletePhoto($participant);

        $em->remove($participant);
        $em->flush();

        $this->addFlash('success', "L'utilisateur ".$participant->getPrenom()." ".$participant->getNom()." a été supprimé !");

        return $this->redirectToRoute('app_admin_utilisateurs');
    }

    // màj des données utilisateurs
    #[Route('/utilisateurs/update/{id}', name: '_utilisateurs/update')]
    public function updateUtilisateur(?Participant $participant, EntityManagerInterface $em, Request $request , PhotoUploader $photoUploader, HashPassword $hashPassword) : Response
    {
        if (!$participant){
            $participant = new Participant();
        }

        $formUser = $this->createForm(ProfileType::class, $participant);
        $formUser->handleRequest($request);

        if ($formUser->isSubmitted() and $formUser->isValid()){

            // Récupération la valeur directe du champ plainPassword du formulaire
            $plainPassword = $formUser->get('plainPassword')->getData();

            // Gestion photo de profil
            if($formUser->get('image_file')->getData() instanceof UploadedFile){
                $photo = $formUser->get('image_file')->getData();
                $filename = $photoUploader->photoUpload($participant, $photo);
                $participant->setPhoto($filename);
            }



            // Vérifie si un nouveau mot de passe a été fourni
            if ($plainPassword) {
                // Hasher le mot de passe
               $password = $hashPassword->hashPassword($plainPassword);
               $participant->setPassword($password);
            }

            $em->persist($participant);
            $em->flush();

            $this->addFlash('success', 'Utilisateur ' . $participant->getPrenom() . " " . $participant->getNom() . " ajouté ou modifié !");

            return $this->redirectToRoute('app_admin_utilisateurs');
        }

        return $this->render('admin/adminUtilisateursUpdate.html.twig', [
            'form'=>$formUser->createView(),
            'user'=>$participant
        ]);
    }

    // Modification du role de l'utilisateur
    #[Route('/utilisateurs/setRole/{id}',name: "_utilisateurs/setRole")]
    public function setRole(Participant $participant, EntityManagerInterface $em) : Response
    {
        if ($participant->isAdministrateur()) {
            $participant->setAdministrateur(false);
            $participant->setRoles(["ROLE_USER"]);
        } else {
            $participant->setAdministrateur(true);
            $participant->setRoles(["ROLE_ADMIN"]);
        }

        $em->persist($participant);
        $em->flush();

        $this->addFlash('success', "Le role de l'utilisateur ".$participant->getPrenom()." ".$participant->getNom()." a été mis à jour");

        return $this->redirectToRoute("app_admin_utilisateurs");
    }

    // Modification du statut utilisateur (Actif/Inactif)
    #[Route('/utilisateurs/actif/{id}', name: '_utilisteurs/actif')]
    public function setActif(Participant $participant, EntityManagerInterface $em): Response
    {
        if($participant->isActif()){
            $participant->setActif(false);
        } else {
            $participant->setActif(true);
        }

        $em->persist($participant);
        $em->flush();

        $this->addFlash('success', "Le statut de l'utilisateur ".$participant->getPrenom()." ".$participant->getNom()." a été mis à jour");

        return $this->redirectToRoute("app_admin_utilisateurs");
    }


// ADMINISTRATION DES VILLES

    #[Route('/villes', name: '_villes')]
    public function adminVille(Request $request,VilleRepository $villeRepository, EntityManagerInterface $em) : Response
    {
        if ($request->query->get('keyword')){
            $villes = $villeRepository ->findByKeyword($request->query->get('keyword'));
        } else {
            $villes = $villeRepository->findAll();
        }

        $newVille = new Ville();
        $form = $this->createForm(VilleType::class,$newVille);
        $form->handleRequest($request);

        if($form->isSubmitted() and  $form->isValid()){
            $em->persist($newVille);
            $em->flush();

            $this->addFlash('success',"La ville ".$newVille->getNom()." a été ajoutée !");

            return $this->redirectToRoute('app_admin_villes');
        }

        return $this->render('admin/adminVilles.html.twig', [
            'villes'=>$villes,
            'form'=>$form->createView(),
        ]);
    }

    //suppression d'une ville
    #[Route('/villes/delete/{id}', name: "_villes_delete")]
    public function deleteVille(Ville $ville, EntityManagerInterface $em) : Response
    {
        try {
            $em->remove($ville);
            $em->flush();

            $this->addFlash("success", "La ville ".$ville->getNom()." a été supprimée !");
        } catch (ForeignKeyConstraintViolationException $exception){
            $this->addFlash("danger", "Impossible de supprimer la ville ".$ville->getNom()." car elle est liée à d'autres éléments (Lieux).");
        } catch (\Exception $exception) {
            $this->addFlash("danger", "Une erreur s'est produite lors de la suppression de la ville ".$ville->getNom()." : ".$exception->getMessage());
        }

        return $this->redirectToRoute("app_admin_villes");
    }



    // ADMINISTRATION DES SORTIES

}
