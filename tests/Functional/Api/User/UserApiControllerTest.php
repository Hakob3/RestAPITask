<?php

namespace App\Tests\Functional\Api\User;

use App\Entity\Client;
use App\Tests\Functional\Api\AbstractApiCase;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Component\HttpFoundation\Request;

class UserApiControllerTest extends AbstractApiCase
{
    /**
     * @throws ORMException
     * @throws OptimisticLockException
     *
     * @group test
     */
    public function testGetAuthClient(): void
    {
        $client = $this->createClientUser();
        $this->em->flush();

        $newClientData = $this->em->getRepository(Client::class)->find($client->getId());

        $response = $this->apiRequest(
            $this->router->generate('api_user_get_auth_client'),
            'GET',
            headers: [
                'HTTP_AUTHORIZATION' => sprintf(
                    'Bearer %s',
                    $this->getAuthToken(self::CLIENT_EMAIL, self::PASSWORD)
                ),
            ]
        );

        $this->assertEquals(200, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);

        $this->assertEquals($newClientData->getId(), $content['item']['id']);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     *
     * @group test
     */
    public function testRegistration(): void
    {
        $this->em->flush();

        $response = $this->apiRequest(
            $this->router->generate('api_user_registration'),
            Request::METHOD_POST,
            headers: [
                'CONTENT_TYPE' => 'application/json',
            ],
            content: json_encode(
                [
                    "email" => self::EMAIL,
                    "password" => self::PASSWORD,
                ]
            )
        );
        $content = json_decode($response->getContent(), true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(self::EMAIL, $content['item']['email']);
    }

    /**
     * @param $statusCode
     * @param $requestBody
     * @throws ORMException
     * @throws OptimisticLockException
     * @dataProvider addDataProvider
     *
     * @group test
     */
    public function testCreateOrEditProfile(
        $statusCode,
        $requestBody
    ): void
    {
        $this->createUser();
        $this->em->flush();

        $response = $this->apiRequest(
            $this->router->generate('api_user_create_or_edit_client'),
            Request::METHOD_POST,
            headers: [
                'HTTP_AUTHORIZATION' => sprintf(
                    'Bearer %s',
                    $this->getAuthToken(self::EMAIL, self::PASSWORD)
                ),
                'CONTENT_TYPE' => 'application/json',
            ],
            content: json_encode($requestBody)
        );

        $this->assertEquals($statusCode, $response->getStatusCode());
    }

    /**
     * @return array
     */
    public function addDataProvider(): array
    {
        return [
            "createCurrentClient" => [
                "statusCode" => 200,
                "requestBody" => [
                    "address" => "Moscow",
                    "phoneNumber" => "+79999999999",
                    "birthday" => "2000-05-05"
                ]
            ],
            "createInvalidClient" => [
                "statusCode" => 207,
                "requestBody" => [
                    "address" => "Moscow",
                    "phone" => "+79999999999",
                    "birthday" => "2000-05-05"
                ]
            ]
        ];
    }
}