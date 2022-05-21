<?php

namespace App\Tests\Controller\Contacts;

use App\Helpers\EnumManager\Enums\GeneralEnum;
use Symfony\Component\HttpFoundation\Response;

class SearchForContactsByNameTest extends BaseContact
{
    public function testIamTryingToSearchForContactsByName(): void
    {
        /**
         * I will assume that there is a contact with first name "Nour Eddin + random_string"
         */
        $search_name = 'Nour Eddin_' . $this->getRandomString();

        /**
         * store & fetch a new contact from the DB directly where the first name of it = $search_name
         */
        $contact = $this->initiateAnewContact($search_name);
        $this->assertNotNull($contact);

        /**
         * try to fetch the same contact object using the api/contacts/search/:name endpoint
         */
        $url = $this->appURL . '/api/contacts/search/' . $contact->getFirstName();
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
        $this->assertTrue(is_array($jsonResponse['data']));
        $this->assertTrue($jsonResponse['data'] > 0);

        /**
         * the assertion statements for contact
         */
        $this->assertEquals($jsonResponse['data']['items'][0]['id'], $contact->getId());
        $this->assertEquals($jsonResponse['data']['items'][0]['firstName'], $contact->getFirstName());
        $this->assertEquals($jsonResponse['data']['items'][0]['lastName'], $contact->getLastName());
        $this->assertEquals($jsonResponse['data']['items'][0]['email'], $contact->getEmail());
        $this->assertEquals($jsonResponse['data']['items'][0]['address'], $contact->getAddress());
        $this->assertEquals($jsonResponse['data']['items'][0]['phoneNumber'], $contact->getPhoneNumber());
        $this->assertEquals($jsonResponse['data']['items'][0]['picture'], $contact->getPicture());
    }

    public function testIamTryingToSearchForContactsByNameWithoutToken(): void
    {
        /**
         * I will assume that there is a contact with first name "Nour Eddin + random_string"
         */
        $search_name = 'Nour Eddin_' . $this->getRandomString();

        /**
         * store & fetch a new contact from the DB directly where the first name of it = $search_name
         */
        $contact = $this->initiateAnewContact($search_name);

        /**
         * try to fetch the same contact object using the api/contacts/search/:name endpoint
         */
        $url = $this->appURL . '/api/contacts/search/' . $contact->getFirstName();
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
