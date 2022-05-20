<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Client;
use App\Entity\Contact;
use App\Helpers\EnumManager\Enums\GeneralEnum;
use App\Helpers\Exceptions\UserException;
use App\Repository\ClientRepository;
use App\Repository\ContactRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ContactService
{

    private EntityManagerInterface $entityManager;
    private TranslatorInterface $translator;
    private ContactRepository $contactRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        TranslatorInterface $translator,
        ContactRepository $contactRepository
    ) {
        $this->entityManager = $entityManager;
        $this->translator = $translator;
        $this->contactRepository = $contactRepository;
    }

    public function addNewContact($data, $client)
    {
        // Adding a new contact ..
        try {
            // begin a new transaction
            $this->entityManager->beginTransaction();

            $contact = new Contact();
            $contact->setFirstName($data['first_name']);
            $contact->setLastName($data['last_name']);
            $contact->setAddress($data['address']);
            $contact->setPhoneNumber($data['phone_number']);
            $contact->setBirthday(new \DateTime($data['birthday']));
            $contact->setEmail($data['email']);
            $contact->setPicture($data['picture']);

            $this->entityManager->persist($contact);

            $this->entityManager->flush();

            $this->entityManager->commit();
        } catch (\Throwable $e)
        {
            $this->entityManager->rollback();
            throw $e;
        }
    }

    public function destroy($id)
    {
        $contact = $this->contactRepository->findOneBy(['id' => $id]);
        if(!$contact)
            throw new UserException($this->translator->trans('Contact not found'), GeneralEnum::NOT_FOUND, Response::HTTP_NOT_FOUND);
        $this->entityManager->remove($contact);
        $this->entityManager->flush();
    }

    // magic method for undefined methods to redirect for repo
    public function __call($method, $args)
    {
        return $this->contactRepository->$method(...$args);
    }
}