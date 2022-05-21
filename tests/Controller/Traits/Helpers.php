<?php

namespace App\Tests\Controller\Traits;

use Symfony\Component\HttpClient\HttpClient;
use DateTime;

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

    public function getAuthToken()
    {
        $this->firstName = $this->getRandomString();
        $this->lastName = $this->getRandomString();
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

    public function getRandomString($length = 10)
    {
        $str = base64_encode(random_bytes($length));
        $str = str_replace('/', '', $str);
        $str = str_replace('\\', '', $str);
        return $str;
    }

    public function getRandomEmail()
    {
        return $this->getRandomString() . '@gmail.com';
    }

    public function getRandomDate()
    {
        return (new DateTime())->format('d-m-Y');
    }

    public function getRandomNumber()
    {
        return rand(1111111111, 9999999999);
    }


}