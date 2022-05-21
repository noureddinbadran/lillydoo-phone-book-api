<?php

namespace App\Tests\Controller\Contacts;

use App\Helpers\EnumManager\Enums\GeneralEnum;
use Symfony\Component\HttpFoundation\Response;

class DeleteContactTest extends BaseContact
{
    public function testIamTryingToDeleteAcontactById(): void
    {
        /**
         * store & fetch a new contact from the DB directly
         */
        $contact = $this->initiateAnewContact();

        /**
         * try to delete the same contact object using the API (DELETE api/contacts/:id)
         */
        $url = $this->appURL . '/api/contacts/' . $contact->getId();
        $response = $this->client->request('DELETE', $url, [
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


        /**
         * Now I will try to fetch the same object using (GET api/contacts/:id) endpoint
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
        $this->assertTrue($statusCode == Response::HTTP_NOT_FOUND);

        /**
         * check the response data
         */
        $this->assertEquals($jsonResponse['metaData']['status'], Response::HTTP_NOT_FOUND);
        $this->assertEquals($jsonResponse['metaData']['key'], GeneralEnum::NOT_FOUND);
        $this->assertEquals($jsonResponse['metaData']['message'], "Contact not found");
    }

    public function testIamTryingToDeleteAcontactByIdWithoutToken(): void
    {
        /**
         * store & fetch a new contact from the DB directly
         */
        $contact = $this->initiateAnewContact();

        /**
         * try to delete the same contact object using the API (DELETE api/contacts/:id)
         */
        $url = $this->appURL . '/api/contacts/' . $contact->getId();
        $response = $this->client->request('DELETE', $url, [
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
