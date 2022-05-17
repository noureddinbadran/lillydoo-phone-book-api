<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Client;
use App\Repository\ClientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AuthService
{
    private $clientRepository;
    private $logger;
    private $entityManager;
    private $userPasswordHasher;

    public function __construct(ClientRepository $clientRepository, LoggerInterface $logger, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->clientRepository = $clientRepository;
        $this->logger = $logger;
        $this->entityManager = $entityManager;
        $this->userPasswordHasher = $userPasswordHasher;
    }

    public function register($data)
    {
        try {
            $this->logger->error('yes');
            $client = new Client();
            $client->setUsername($data['username']);
            // hashing the password before store it
            $password = $this->userPasswordHasher->hashPassword($client, $data['password']);
            $client->setPassword($password);
            $client->setFirstName($data['first_name']);
            $client->setLastName($data['last_name']);
            $client->setCreated(new \DateTime());

            $this->clientRepository->add($client);

            return true;
        } catch (\Throwable $e)
        {
            $this->logger->error($e->getMessage());
            return false;
        }
    }
}