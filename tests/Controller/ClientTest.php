<?php

namespace App\Tests\Controller;

use App\Entity\Client;
use App\Repository\ClientRepository;
use DateTime;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use  Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use function Symfony\Component\DependencyInjection\Loader\Configurator\env;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Response;

class ClientTest extends WebTestCase
{

    private $appURL;
    private $firstName;
    private $lastName;
    private $username;
    private ?EntityManager $entityManager;

    protected function setUp(): void
    {
        $this->appURL = 'http://localhost:8000';
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->firstName = base64_encode(random_bytes(10));
        $this->lastName = base64_encode(random_bytes(10));
        $this->username = $this->firstName . $this->lastName .'@gmail.com';
    }


    public function testIamTryingToRegisterAnewClient(): void
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

        // check the status of the response
        $statusCode = $response->getStatusCode();
        $this->assertTrue($statusCode == Response::HTTP_OK);

        // check the database
        $clientRepo = $this->entityManager->getRepository(Client::class);
        $client = $clientRepo->findOneBy([
            'username' => $this->username
        ]);

        $this->assertNotNull($client);
        $this->assertNotNull($client->getPassword());
        // make sure the password it's not a plain text
        $this->assertNotEquals($client->getPassword(), '12345678');
        $this->assertEquals($client->getFirstName(), $this->firstName);
        $this->assertEquals($client->getLastName(), $this->lastName);
    }

    public function testIamTryingToRegisterAnewClientWithAnExistedUserName(): void
    {
        // step1(valid registration)
        $this->firstName = base64_encode(random_bytes(10));
        $this->lastName = base64_encode(random_bytes(10));
        $this->username = $this->firstName . $this->lastName .'@gmail.com';

        $this->testIamTryingToRegisterAnewClient();

        // step2(try to register again with same info)
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

        // check the status of the response
        $statusCode = $response->getStatusCode();
        $this->assertTrue($statusCode == Response::HTTP_BAD_REQUEST);
    }

    public function testIamTryingToRegisterAnewClientWithoutData(): void
    {
        $client = HttpClient::create();
        $url = $this->appURL . '/api/auth/register';

        $response = $client->request('POST', $url , [
            'json' => [
            ]
        ]);

        // check the status of the response
        $statusCode = $response->getStatusCode();
        $this->assertTrue($statusCode == Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testIamTryingToRegisterAnewClientWithShortPassword(): void
    {
        $client = HttpClient::create();
        $url = $this->appURL . '/api/auth/register';

        $response = $client->request('POST', $url , [
            'json' => [
                'username' => $this->username,
                'password' => "1",
                'first_name' => $this->firstName,
                'last_name' => $this->lastName,
            ]
        ]);

        // check the status of the response
        $statusCode = $response->getStatusCode();
        $this->assertTrue($statusCode == Response::HTTP_BAD_REQUEST);
    }

    public function testIamTryingToRegisterAnewClientWithNullValues(): void
    {
        $client = HttpClient::create();
        $url = $this->appURL . '/api/auth/register';

        $response = $client->request('POST', $url , [
            'json' => [
                'username' => null,
                'password' => null,
                'first_name' => null,
                'last_name' => null,
            ]
        ]);

        // check the status of the response
        $statusCode = $response->getStatusCode();
        $this->assertTrue($statusCode == Response::HTTP_BAD_REQUEST);
    }

    public function testIamTryingToLoginUsingInvalidCredentialsOrUnregisteredClient(): void
    {
        $this->firstName = base64_encode(random_bytes(10));
        $this->lastName = base64_encode(random_bytes(10));
        $this->username = $this->firstName . $this->lastName .'@gmail.com';

        $client = HttpClient::create();
        $url = $this->appURL . '/api/login_check';

        $response = $client->request('POST', $url , [
            'json' => [
                'username' => $this->username,
                'password' => 'wrong_password',
            ]
        ]);

        // check the status of the response
        $statusCode = $response->getStatusCode();
        $this->assertTrue($statusCode == Response::HTTP_UNAUTHORIZED);
    }

    public function testIamTryingToLoginUsingCorrectCredentials(): void
    {
        $this->firstName = base64_encode(random_bytes(10));
        $this->lastName = base64_encode(random_bytes(10));
        $this->username = $this->firstName . $this->lastName .'@gmail.com';

        // register a new client
        $this->testIamTryingToRegisterAnewClient();

        $client = HttpClient::create();
        $url = $this->appURL . '/api/login_check';

        $response = $client->request('POST', $url , [
            'json' => [
                'username' => $this->username,
                'password' => '12345678',
            ]
        ]);

        // check the status of the response
        $statusCode = $response->getStatusCode();
        $this->assertTrue($statusCode == Response::HTTP_OK);

        // make sure we received a token
        $jsonResponse = json_decode($response->getContent());

        $this->assertNotNull($jsonResponse);
        $this->assertNotNull($jsonResponse->token);
    }
}
