<?php

namespace App\Tests\Controller;

use  Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpClient\HttpClient;


class BaseTestCase extends WebTestCase
{
    protected $client;
    protected $authToken;
    protected $appURL;

    protected function setUp(): void
    {
        $this->client = HttpClient::create();
        $this->appURL = 'http://localhost:8000';
    }
}