<?php

namespace App\Services;

use App\Entity\Participant;

class HashPassword
{

    public function hashPassword (Participant $user, $passwordToHash) : void
    {
        $hashedPassword = password_hash($passwordToHash,PASSWORD_BCRYPT);
        $user->setPassword($hashedPassword);
    }

}