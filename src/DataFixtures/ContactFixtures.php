<?php

namespace App\DataFixtures;

use App\Entity\Contact;
use App\Tests\Controller\Traits\Helpers;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ContactFixtures extends Fixture
{
    use Helpers;

    public function load(ObjectManager $manager)
    {
        $contacts = [
          [
              'first_name' => $this->getRandomString(),
              'last_name' => $this->getRandomString(),
              'email' => $this->getRandomEmail(),
              'phone_number' => (string)$this->getRandomNumber(),
              'birthday' => new DateTime(),
              'address' => $this->getRandomString(),
              'picture' => 'base64_image'
          ],
            [
                'first_name' => $this->getRandomString(),
                'last_name' => $this->getRandomString(),
                'email' => $this->getRandomEmail(),
                'phone_number' => (string)$this->getRandomNumber(),
                'birthday' => new DateTime(),
                'address' => $this->getRandomString(),
                'picture' => 'base64_image'
            ]
        ];

        foreach ($contacts as $contact)
        {
            $entity = new Contact();
            $entity->setFirstName($contact['first_name']);
            $entity->setLastName($contact['last_name']);
            $entity->setPhoneNumber($contact['phone_number']);
            $entity->setEmail($contact['email']);
            $entity->setAddress($contact['address']);
            $entity->setBirthday($contact['birthday']);
            $entity->setPicture($contact['picture']);

            $manager->persist($entity);
        }

        $manager->flush();
    }
}