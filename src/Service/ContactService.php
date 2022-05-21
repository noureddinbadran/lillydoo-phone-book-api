<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Client;
use App\Entity\Contact;
use App\Helpers\EnumManager\Enums\GeneralEnum;
use App\Helpers\Exceptions\UserException;
use App\Repository\ClientRepository;
use App\Repository\ContactRepository;
use App\Service\Traits\HelperTrait;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ContactService
{
    use HelperTrait;

    private EntityManagerInterface $entityManager;
    private TranslatorInterface $translator;
    private ContactRepository $contactRepository;
    private PaginatorInterface $paginator;

    public function __construct(
        EntityManagerInterface $entityManager,
        TranslatorInterface $translator,
        ContactRepository $contactRepository,
        PaginatorInterface $paginator
    )
    {
        $this->entityManager = $entityManager;
        $this->translator = $translator;
        $this->contactRepository = $contactRepository;
        $this->paginator = $paginator;
    }

    /**
     * @param $page
     * @param $limit
     * @return array
     */
    public function getContacts($page, $limit)
    {
        $contactsQuery = $this->contactRepository->createQueryBuilder('c')
            ->orderBy('c.id', 'desc')
            ->getQuery();
        return $this->paginate($contactsQuery, $page, $limit);
    }

    /**
     * @param $name
     * @param $page
     * @param $limit
     * @return mixed
     */
    public function searchByName($name, $page, $limit)
    {
        $contactsQuery = $this->contactRepository->createQueryBuilder('c')
            ->andWhere("concat(c.first_name, ' ', c.last_name) LIKE :name")
            ->setParameter('name', "%$name%")
            ->orderBy('c.id', 'desc')
            ->getQuery();
        return $this->paginate($contactsQuery, $page, $limit);
    }

    /**
     * @param $data
     * @param Contact|null $given_contact
     * @return Contact
     * @throws \Throwable
     */
    public function createOrUpdateContact($data, Contact $given_contact = null)
    {
        try {
            // begin a new transaction
            $this->entityManager->beginTransaction();

            $contact = $given_contact ?? new Contact();
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

            return $contact;
        } catch (\Throwable $e) {
            $this->entityManager->rollback();
            throw $e;
        }
    }

    /**
     * @param $id
     * @throws UserException
     */
    public function destroy($id)
    {
        $contact = $this->contactRepository->findOneBy(['id' => $id]);
        if (!$contact)
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