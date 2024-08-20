<?php

namespace App\Controller\Api;

use App\Controller\AppAbstractController;
use App\DTO\Response\User\ClientDTO;
use App\DTO\Response\User\UserDTO;
use App\DTO\Transformer\User\ClientDTOTransformer;
use App\DTO\Transformer\User\UserDTOTransformer;
use App\Entity\Client;
use App\Entity\User;
use App\Exception\ApiException;
use App\Form\Api\User\ClientAPIFormType;
use App\Form\Api\User\RegistrationAPIFormType;
use App\Service\ApiDocumentation\PrepareAuthorizationDocumentation;
use App\Service\ApiDocumentation\PrepareJsonRequestDocumentation;
use App\Service\ApiDocumentation\PrepareSingleDataDocumentation;
use Doctrine\ORM\NonUniqueResultException;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use OpenApi\Attributes as OA;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[OA\Tag(
    name: 'Пользователь(User)',
)]
#[Route('/api/user', name: 'api_user_')]
class UserApiController extends AppAbstractController
{
    /**
     * @param ClientDTOTransformer $clientDTOTransformer
     * @return Response
     */
    #[PrepareAuthorizationDocumentation]
    #[OA\Response(
        response: 200,
        description: 'Клиент',
        content: new PrepareSingleDataDocumentation(dto: ClientDTO::class)
    )]
    #[OA\Get(
        description: 'Получить данные авторизованного клиента',
        summary: 'Получить данные авторизованного клиента - api_user_get_auth_client'
    )]
    #[Route('/client', name: 'get_auth_client', methods: ['GET'])]
    #[IsGranted('ROLE_CLIENT')]
    public function getAuthClient(
        ClientDTOTransformer $clientDTOTransformer
    ): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        return $this->prepareSingleDataResponse(
            $user->getClient(),
            $clientDTOTransformer
        );
    }

    /**
     * @param Request $request
     * @param UserDtoTransformer $userDtoTransformer
     * @return Response
     * @throws NonUniqueResultException
     * @throws ApiException
     */
    #[OA\Response(
        response: 200,
        description: 'Успешная регистрация пользователя',
        content: new PrepareSingleDataDocumentation(dto: UserDTO::class)
    )]
    #[OA\RequestBody(
        description: 'Регистрация нового пользователя',
        content: new OA\JsonContent(
            ref: new Model(type: RegistrationAPIFormType::class),
            type: 'object',
            example: ['email' => 'user@mail.ru', 'password' => 'pa$$word']
        )
    )]
    #[OA\Post(
        description: 'Регистрация пользователя',
        summary: 'Регистрация пользователя - api_user_registration')
    ]
    #[Route('/registration', name: 'registration', methods: ['POST'])]
    public function userRegistration(
        Request            $request,
        UserDTOTransformer $userDtoTransformer
    ): Response
    {
        $user = new User();
        $user->setRoles([User::ROLE_USER]);

        return $this->responseApiForm(
            $request,
            $user,
            RegistrationAPIFormType::class,
            $userDtoTransformer
        );
    }

    /**
     * @param Request $request
     * @param ClientDTOTransformer $clientDTOTransformer
     * @return Response
     * @throws ApiException
     */
    #[PrepareAuthorizationDocumentation]
    #[OA\Response(
        response: 200,
        description: 'Клиент успешно создан(отредактирован)',
        content: new PrepareSingleDataDocumentation(ClientDTO::class)
    )]
    #[OA\RequestBody(
        description: 'Анкета клиента',
        content: new PrepareJsonRequestDocumentation(formType: ClientAPIFormType::class)
    )]
    #[OA\Post(
        description: 'Создание(редактирование) клиента',
        summary: 'Создание(редактирование) клиента - api_user_create_or_edit_client')
    ]
    #[Route('/client', name: 'create_or_edit_client', methods: ['POST'])]
    #[IsGranted("ROLE_USER")]
    public function createOrEditProfile(
        Request              $request,
        ClientDTOTransformer $clientDTOTransformer
    ): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $client = $user->getClient();
        if ($client === null) {
            $client = new Client();
            $client->setUser($user);
        }

        return $this->responseApiForm(
            $request,
            $client,
            ClientAPIFormType::class,
            $clientDTOTransformer
        );
    }
}