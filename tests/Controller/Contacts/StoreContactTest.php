<?php

namespace App\Tests\Controller\Contacts;

use App\Entity\Contact;
use App\Helpers\EnumManager\Enums\GeneralEnum;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Response;

class StoreContactTest extends BaseContact
{
    public function testIamTryingToAddAnewContact(): void
    {
        $url = $this->appURL . '/api/contacts';
        $response = $this->client->request('POST', $url , [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->authToken
            ],
            'json' => [
                'first_name' => $this->firstName,
                'last_name' => $this->lastName,
                'address' => $this->address,
                'phone_number' => (string)$this->phone_number,
                'birthday' => $this->birthday,
                'email' => $this->email,
                'picture' => $this->picture,
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
         * check the database
         */
        $contactRepo = $this->entityManager->getRepository(Contact::class);
        $contact = $contactRepo->findOneBy([
            'email' => $this->email
        ]);

        $this->assertNotNull($contact);
        $this->assertEquals($contact->getFirstName(), $this->firstName);
        $this->assertEquals($contact->getLastName(), $this->lastName);
        $this->assertEquals($contact->getEmail(), $this->email);
        $this->assertEquals($contact->getPhoneNumber(), $this->phone_number);
        $this->assertEquals($contact->getBirthday()->format('d-m-Y'), $this->birthday);
        $this->assertEquals($contact->getAddress(), $this->address);
        $this->assertEquals($contact->getPicture(), $this->picture);

        /**
         * check the response data
         */
        $this->assertEquals($jsonResponse['metaData']['status'], Response::HTTP_OK);
        $this->assertEquals($jsonResponse['metaData']['key'], GeneralEnum::SUCCESS);
        $this->assertEquals($jsonResponse['metaData']['message'], "");
    }


    public function testIamTryingToAddAnewContactWithoutRequiredData(): void
    {
        $url = $this->appURL . '/api/contacts';
        $response = $this->client->request('POST', $url, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->authToken
            ],
            'json' => [
            ]
        ]);

        $statusCode = $response->getStatusCode();
        $jsonResponse = json_decode($response->getContent(false), true);

        /**
         * check the request status code
         */
        $this->assertTrue($statusCode == Response::HTTP_BAD_REQUEST);

        /**
         * check the response data
         */
        $this->assertEquals($jsonResponse['metaData']['status'], Response::HTTP_BAD_REQUEST);
        $this->assertEquals($jsonResponse['metaData']['key'], GeneralEnum::VALIDATION);
        $this->assertEquals($jsonResponse['metaData']['message'], "First name can't be left blank,Last name can't be left blank,Address can't be left blank,Phone number can't be left blank,Birthday can't be left blank,Email can't be left blank");
    }

    public function testIamTryingToAddAnewContactWithoutAuthToken(): void
    {
        $url = $this->appURL . '/api/contacts';
        $response = $this->client->request('POST', $url, [
            'headers' => [

            ],
            'json' => [
                'first_name' => $this->firstName,
                'last_name' => $this->lastName,
                'address' => $this->address,
                'phone_number' => (string)$this->phone_number,
                'birthday' => $this->birthday,
                'email' => $this->email,
                'picture' => $this->picture,
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
        $this->assertEquals($jsonResponse['code'], Response::HTTP_UNAUTHORIZED);
        $this->assertEquals($jsonResponse['message'], 'JWT Token not found');
    }
}
