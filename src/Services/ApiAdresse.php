<?php

namespace App\Services;

use App\Entity\Lieu;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ApiAdresse
{
    private $client;

    /**
     * @param $client
     */
    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function getDatas(string $street, string $codePost) : array
    {
        $codePostal = $codePost;
        $rue = $street;

        $response = $this->client->request(
            'GET',
            'https://api-adresse.data.gouv.fr/search/?q='.$rue.'&postcode='.$codePostal.'&limit=5&type=street'
        );
        $array = $response->toArray();

        $newArray = [];

        foreach ($array['features'] as $feature) {
            $newArray[] = [
                'id' => $feature['properties']['id'],
                'name' => $feature['properties']['name'],
                'postcode' => $feature['properties']['postcode'],
                'city' => $feature['properties']['city'],
                'lat' => $feature['geometry']['coordinates'][0],
                'long' => $feature['geometry']['coordinates'][1],
            ];
        }
        return $newArray;
    }

}