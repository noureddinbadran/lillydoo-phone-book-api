<?php

namespace App\Tests\Controller\Traits;

use Symfony\Component\HttpClient\HttpClient;

trait Helpers
{
    public function registerNewClient()
    {
        $client = HttpClient::create();
        $url = $this->appURL . '/api/auth/register';
        $response = $client->request('POST', $url , [
            'json' => [
                'username' => $this->username,
                'password' => "12345678",
                'first_name' => $this->firstName,
                'last_name' => $this->lastName,
            ]
        ]);
    }

    public  function getAuthToken()
    {
        $this->firstName = base64_encode(random_bytes(10));
        $this->lastName = base64_encode(random_bytes(10));
        $this->username = $this->firstName . $this->lastName .'@gmail.com';

        // register a new client
        $this->registerNewClient();

        $client = HttpClient::create();
        $url = $this->appURL . '/api/login_check';

        $response = $client->request('POST', $url , [
            'json' => [
                'username' => $this->username,
                'password' => "12345678",
            ]
        ]);

        $jsonResponse = json_decode($response->getContent());
        return $jsonResponse->token;
    }


}