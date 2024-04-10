<?php

namespace App\Services;

use App\Entity\Participant;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class PhotoUploader
{
    private SluggerInterface $slugger;
    private const UPLOAD_DIR = 'images/profil';

    /**
     * @param SluggerInterface $slugger
     */public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    public function photoUpload(Participant $user,UploadedFile $photo) : string
    {
        $this->deletePhoto($user);
        $fileName = $this->slugger->slug($user->getNom().$user->getPrenom()).'.'.uniqid().'.'.$photo->guessExtension();
        $photo->move(self::UPLOAD_DIR, $fileName);
        return $fileName;
    }

    public function deletePhoto(Participant $user) : void
    {
        if($user->getPhoto() and file_exists('images/profil/'.$user->getPhoto())){
            unlink(self::UPLOAD_DIR.$user->getPhoto());
        }
    }
}