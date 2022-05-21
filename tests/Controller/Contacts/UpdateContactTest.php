<?php

namespace App\Tests\Controller\Contacts;

use App\Entity\Contact;
use App\Helpers\EnumManager\Enums\GeneralEnum;
use App\Tests\Controller\Traits\ContactTrait;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Response;

class UpdateContactTest extends BaseContact
{
    public function testIamTryingToUpdateAnExistedContact(): void
    {
        /**
         * store & fetch a new contact from the DB directly
         */
        $contact = $this->initiateAnewContact();

        /**
         * try to update the info of the contact using the (UPDATE api/contacts/:id) endpoint
         */
        $newFirstName = $this->getRandomString();
        $newLastName = $this->getRandomString();
        $newAddress = $this->getRandomString();
        $newPhoneNumber = (string)$this->getRandomNumber();
        $newBirthday = $this->getRandomDate();
        $newEmail = $this->getRandomEmail();
        $newPicture = $this->getRandomString();

        $url = $this->appURL . '/api/contacts/' . $contact->getId();
        $response = $this->client->request('PUT', $url, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->authToken
            ],
            'json' => [
                'first_name' => $newFirstName,
                'last_name' => $newLastName,
                'address' => $newAddress,
                'phone_number' => $newPhoneNumber,
                'birthday' => $newBirthday,
                'email' => $newEmail,
                'picture' => $newPicture,
            ]
        ]);

        /**
         * check the status of the response
         */
        $statusCode = $response->getStatusCode();
        $jsonResponse = json_decode($response->getContent(false), true);

        /**
         * check the request status code
         */
        $this->assertTrue($statusCode == Response::HTTP_OK);

        /**
         * check the response data
         */
        $this->assertEquals($jsonResponse['metaData']['status'], Response::HTTP_OK);
        $this->assertEquals($jsonResponse['metaData']['key'], GeneralEnum::SUCCESS);
        $this->assertEquals($jsonResponse['metaData']['message'], "");
        $this->assertEquals($jsonResponse['data']['id'], $contact->getId());
        $this->assertNotEquals($jsonResponse['data']['firstName'], $contact->getFirstName());
        $this->assertNotEquals($jsonResponse['data']['lastName'], $contact->getLastName());
        $this->assertNotEquals($jsonResponse['data']['address'], $contact->getAddress());
        $this->assertNotEquals($jsonResponse['data']['phoneNumber'], $contact->getPhoneNumber());
        $this->assertNotEquals($jsonResponse['data']['email'], $contact->getEmail());
        $this->assertNotEquals($jsonResponse['data']['picture'], $contact->getPicture());

        $this->assertEquals($jsonResponse['data']['firstName'], $newFirstName);
        $this->assertEquals($jsonResponse['data']['lastName'], $newLastName);
        $this->assertEquals($jsonResponse['data']['address'], $newAddress);
        $this->assertEquals($jsonResponse['data']['phoneNumber'], $newPhoneNumber);
        $this->assertEquals($jsonResponse['data']['email'], $newEmail);
        $this->assertEquals($jsonResponse['data']['picture'], $newPicture);
    }

    public function testIamTryingToUpdateAnExistedContactWithoutToken(): void
    {
        /**
         * store & fetch a new contact from the DB directly
         */
        $contact = $this->initiateAnewContact();

        /**
         * try to update the info of the contact using the (UPDATE api/contacts/:id) endpoint
         */
        $newFirstName = $this->getRandomString();
        $newLastName = $this->getRandomString();
        $newAddress = $this->getRandomString();
        $newPhoneNumber = (string)$this->getRandomNumber();
        $newBirthday = $this->getRandomDate();
        $newEmail = $this->getRandomEmail();
        $newPicture = $this->getRandomString();

        $url = $this->appURL . '/api/contacts/' . $contact->getId();
        $response = $this->client->request('PUT', $url, [
            'headers' => [
                'Authorization' => ''
            ],
            'json' => [
                'first_name' => $newFirstName,
                'last_name' => $newLastName,
                'address' => $newAddress,
                'phone_number' => $newPhoneNumber,
                'birthday' => $newBirthday,
                'email' => $newEmail,
                'picture' => $newPicture,
            ]
        ]);

        $statusCode = $response->getStatusCode();
        $jsonResponse = json_decode($response->getContent(false), true);

        /**
         * check the request status code
         */
        $this->assertTrue($statusCode == Response::HTTP_UNAUTHORIZED);

        /**
         * check the response data
         */
        /**
         * check the response data
         */
        $this->assertEquals($jsonResponse['code'], Response::HTTP_UNAUTHORIZED);
        $this->assertEquals($jsonResponse['message'], 'JWT Token not found');
    }
}
