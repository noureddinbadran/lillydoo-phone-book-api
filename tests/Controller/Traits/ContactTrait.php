<?php

namespace App\Tests\Controller\Traits;

use App\Entity\Contact;
use DateTime;
use Symfony\Component\HttpFoundation\Response;

trait ContactTrait
{
    public function initiateAnewContact($first_name = null, $last_name = null)
    {
        $email = $this->getRandomString() . '@gmail.com';
        $url = $this->appURL . '/api/contacts';
        $response = $this->client->request('POST', $url , [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->authToken
            ],
            'json' => [
                'first_name' => $first_name ?? $this->getRandomString(),
                'last_name' => $last_name ?? $this->getRandomString(),
                'address' => $this->getRandomString(),
                'phone_number' => (string)$this->getRandomNumber(),
                'birthday' => (new DateTime())->format('d-m-Y'),
                'email' => $email,
                'picture' => $this->getRandomString(),
            ]
        ]);


        // check the status of the response
        $statusCode = $response->getStatusCode();

        // check the request status code
        $this->assertTrue($statusCode == Response::HTTP_OK);

        $contactRepo = $this->entityManager->getRepository(Contact::class);
        $contact = $contactRepo->findOneBy([
            'email' => $email
        ]);

        $this->entityManager->clear();
        $this->entityManager->flush();

        return $contact;
    }
}