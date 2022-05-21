<?php

namespace App\Tests\Controller\Contacts;

use App\Helpers\EnumManager\Enums\GeneralEnum;
use Symfony\Component\HttpFoundation\Response;

class GetContactByIdTest extends BaseContact
{

    public function testIamTryingToGetAcontactById(): void
    {
        /**
         * store & fetch a new contact from the DB directly
         */
        $contact = $this->initiateAnewContact();

        /**
         * try to fetch the same contact object using the api/contacts/:id endpoint
         */
        $url = $this->appURL . '/api/contacts/' . $contact->getId();
        $response = $this->client->request('GET', $url, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->authToken
            ],
        ]);

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
        $this->assertEquals($jsonResponse['data']['firstName'], $contact->getFirstName());
        $this->assertEquals($jsonResponse['data']['lastName'], $contact->getLastName());
        $this->assertEquals($jsonResponse['data']['email'], $contact->getEmail());
        $this->assertEquals($jsonResponse['data']['address'], $contact->getAddress());
        $this->assertEquals($jsonResponse['data']['phoneNumber'], $contact->getPhoneNumber());
        $this->assertEquals($jsonResponse['data']['picture'], $contact->getPicture());
    }

    public function testIamTryingToGetAcontactByIdWithoutToken(): void
    {
        /**
         * store & fetch a new contact from the DB directly
         */
        $contact = $this->initiateAnewContact();

        /**
         * try to fetch the same contact object using the api/contacts/:id endpoint
         */
        $url = $this->appURL . '/api/contacts/' . $contact->getId();
        $response = $this->client->request('GET', $url, [
            'headers' => [
                'Authorization' => ''
            ],
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
