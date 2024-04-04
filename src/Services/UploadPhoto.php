<?php

namespace App\Services;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class uploadPhoto
{

    public function uploadPhoto($file, $user, SluggerInterface $slugger)
    {
        // Vérif si présence d'une photo de profil
        if ($file instanceof UploadedFile){
            // suppression de la photo déjà présente
            if($user->getPhoto() && file_exists('images/profil/'.$user->getPhoto())){
                unlink('images/profil/'.$user->getPhoto());
            }

            // standardisation du nom du fichier
            $fileName = $slugger->slug($user->getNom().$user->getPrenom()).uniqid().'.'.$file->guessExtension();
            // renommage et transfert du fichier dans le dossier
            $file->move('images/profil',$fileName);

            $user->setPhoto($fileName);
        }

    }

}