<?php

namespace App\Tests\Controller\Contacts;

use App\Entity\Client;
use App\Entity\Contact;
use App\Helpers\EnumManager\Enums\GeneralEnum;
use App\Repository\ClientRepository;
use App\Tests\Controller\BaseTestCase;
use App\Tests\Controller\Traits\Helpers;
use DateTime;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Response;

class BaseContact extends BaseTestCase
{
    use Helpers;

    protected $firstName;
    protected $lastName;
    protected $email;
    protected $phone_number;
    protected $address;
    protected $birthday;
    protected $picture;
    protected ?EntityManager $entityManager;

    protected function setUp(): void
    {
        parent::setUp();
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->firstName = $this->getRandomString();
        $this->lastName = $this->getRandomString();
        $this->email = $this->firstName . $this->lastName . '@gmail.com';
        $this->phone_number = $this->getRandomNumber();
        $this->address = $this->getRandomString();
        $this->picture = $this->getRandomString();
        $this->birthday = (new DateTime())->format('d-m-Y');

        $this->authToken = $this->getAuthToken();
        $this->assertNotNull($this->authToken);
    }

}
