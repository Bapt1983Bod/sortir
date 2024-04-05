<?php

namespace App\Services;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class PhotoUploader
{

    private SluggerInterface $slugger;

    /**
     * @param SluggerInterface $slugger
     */public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    public function photoUpload($user, $photo) : string
    {
        $this->deletePhoto($user);

        $fileName = $this->slugger->slug($user->getNom().$user->getPrenom()).'.'.uniqid().'.'.$photo->guessExtension();
        $photo->move('images/profil', $fileName);

        return $fileName;
    }

    public function deletePhoto($user) : void
    {
        if($user->getPhoto() and file_exists('images/profil/'.$user->getPhoto())){
            unlink('images/profil/'.$user->getPhoto());
        }
    }



}