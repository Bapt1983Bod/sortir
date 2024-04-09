<?php

namespace App\Services;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class CallApiAdresse
{

    private $client;

    public function __construct(HttpClientInterface $client){
        $this->client = $client;
    }


        public function getData (String $rue, String $city) :array
        {
            $response = $this->client->request(
                'GET',
                'https://api-adresse.data.gouv.fr/search/?q='.$rue.'city='.$city.'&limit=1');


            return $response->toArray();
        }

}