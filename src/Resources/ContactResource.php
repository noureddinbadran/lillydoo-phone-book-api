<?php

namespace App\Resources;

class ContactResource
{
    private $contact;

    public function __construct($contact)
    {
        $this->contact = $contact;
    }

    public function toArray()
    {
        return [
            'id' => $this->contact->getId(),
            'firstName' => $this->contact->getFirstName(),
            'lastName' => $this->contact->getLastName(),
            'email' => $this->contact->getEmail(),
            'phoneNumber' => $this->contact->getPhoneNumber(),
            'address' => $this->contact->getAddress(),
            'birthday' => $this->contact->getBirthday()->format('d-m-Y'),
            'picture' => $this->contact->getPicture(),
        ];
    }
}