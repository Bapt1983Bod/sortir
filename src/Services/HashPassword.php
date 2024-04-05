<?php

namespace App\Services;

use App\Entity\Participant;

class HashPassword
{

    public function hashPassword ($passwordToHash) : string
    {
       return $hashedPassword = password_hash($passwordToHash,PASSWORD_BCRYPT);
    }

}