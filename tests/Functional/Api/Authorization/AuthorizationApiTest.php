<?php

namespace App\Tests\Functional\Api\Authorization;

use App\Tests\Functional\Api\AbstractApiCase;
use Doctrine\ORM\Exception\ORMException;
use Symfony\Component\HttpFoundation\Request;

class AuthorizationApiTest extends AbstractApiCase
{
    /**
     * @dataProvider addDataProvider
     * @group test
     * @throws ORMException
     */
    public function testApiAuthorization(
        bool $wrongUser,
        int  $statusCode
    ): void
    {
        if (!$wrongUser) {
            $this->createUser();
            $this->em->flush();
        }

        $url = $this->router->generate('api_login_check');

        $response = $this->apiRequest(
            $url,
            Request::METHOD_POST,
            headers: [
                'CONTENT_TYPE' => 'application/json',
            ],
            content: json_encode(
                [
                    "email" => $wrongUser ? 'wrong@mail.ru' : self::EMAIL,
                    "password" => $wrongUser ? 'wrongPassword' : self::PASSWORD,
                ]
            )
        );

        $this->assertEquals($response->getStatusCode(), $statusCode);
    }

    /**
     * @return mixed
     */
    public function addDataProvider(): mixed
    {
        return [
            "authorizationWithWrongUser" => [
                "wrongUser" => true,
                "statusCode" => 401
            ],
            "authorizationWithCorrectUser" => [
                "wrongUser" => false,
                "statusCode" => 200
            ]
        ];
    }
}