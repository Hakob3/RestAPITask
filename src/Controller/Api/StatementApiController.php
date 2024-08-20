<?php

namespace App\Controller\Api;

use App\Controller\AppAbstractController;
use App\DTO\Response\StatementDTO;
use App\DTO\Response\User\UserDTO;
use App\DTO\Transformer\StatementDTOTransformer;
use App\Entity\Statement;
use App\Entity\User;
use App\Exception\ApiException;
use App\Form\Api\StatementAPIFormType;
use App\Form\Api\User\RegistrationAPIFormType;
use App\Repository\StatementRepository;
use App\Service\ApiDocumentation\PrepareAuthorizationDocumentation;
use App\Service\ApiDocumentation\PrepareManyDataDocumentation;
use App\Service\ApiDocumentation\PrepareJsonRequestDocumentation;
use App\Service\ApiDocumentation\PrepareSingleDataDocumentation;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use OpenApi\Attributes as OA;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[OA\Tag(
    name: 'Заявления(Statements)',
)]
#[Route('/api/statement', name: 'api_statement_')]
class StatementApiController extends AppAbstractController
{
    /**
     * @param StatementDTOTransformer $statementDTOTransformer
     * @param StatementRepository $statementRepository
     * @return Response
     */
    #[OA\Response(
        response: 200,
        description: 'Список заявлении',
        content: new PrepareManyDataDocumentation(dto: StatementDTO::class)
    )]
    #[OA\Get(
        description: 'Получить заявления',
        summary: 'Получить заявления - api_statement_list'
    )]
    #[Route('', name: 'list', methods: ['GET'])]
    public function getStatements(
        StatementDTOTransformer $statementDTOTransformer,
        StatementRepository     $statementRepository
    ): Response
    {
        return $this->prepareManyDataResponse(
            $statementRepository->findAll(),
            $statementDTOTransformer
        );
    }

    /**
     * @param StatementDTOTransformer $statementDTOTransformer
     * @param Statement $statement
     * @return Response
     */
    #[OA\Response(
        response: 200,
        description: 'Заявление',
        content: new PrepareSingleDataDocumentation(dto: StatementDTO::class)
    )]
    #[OA\PathParameter(
        name: 'id',
        description: 'Идентификатор заявления',
        required: true,
        schema: new OA\Schema(type: 'integer', minimum: 1),
        example: 5
    )]
    #[OA\Get(
        description: 'Получить заявление по id',
        summary: 'Получить заявление по id - api_statement_single'
    )]
    #[Route('/{id}', name: 'single', methods: ['GET'])]
    public function getSingleStatement(
        StatementDTOTransformer $statementDTOTransformer,
        Statement               $statement
    ): Response
    {
        return $this->prepareSingleDataResponse(
            $statement,
            $statementDTOTransformer
        );
    }

    /**
     * @param Request $request
     * @param StatementDTOTransformer $statementDTOTransformer
     * @return Response
     * @throws ApiException
     */
    #[PrepareAuthorizationDocumentation]
    #[OA\Response(
        response: 200,
        description: 'Заявление создано',
        content: new PrepareSingleDataDocumentation(dto: StatementDTO::class)
    )]
    #[OA\RequestBody(
        description: 'Создать заявление',
        content: new PrepareJsonRequestDocumentation(formType: StatementAPIFormType::class)
    )]
    #[OA\Post(
        description: 'Создать заявление',
        summary: 'Создать заявление - api_statement_create'
    )]
    #[Route('', name: 'create', methods: ['POST'])]
    #[IsGranted("create")]
    public function createStatement(
        Request                 $request,
        StatementDTOTransformer $statementDTOTransformer
    ): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $client = $user->getClient();

        $statement = new Statement();
        $statement->setClient($client);

        return $this->responseApiForm(
            $request,
            $statement,
            StatementAPIFormType::class,
            $statementDTOTransformer
        );
    }

    /**
     * @param Request $request
     * @param StatementDTOTransformer $statementDTOTransformer
     * @param Statement $statement
     * @return Response
     * @throws ApiException
     */
    #[PrepareAuthorizationDocumentation]
    #[OA\Response(
        response: 200,
        description: 'Заявление редактировано',
        content: new PrepareSingleDataDocumentation(dto: StatementDTO::class)
    )]
    #[OA\RequestBody(
        description: 'Редактировать заявление',
        content: new PrepareJsonRequestDocumentation(formType: StatementAPIFormType::class)
    )]
    #[OA\PathParameter(
        name: 'id',
        description: 'Идентификатор заявления',
        required: true,
        schema: new OA\Schema(type: 'integer', minimum: 1),
        example: 5
    )]
    #[OA\Patch(
        description: 'Редактировать заявление',
        summary: 'Редактировать заявление - api_statement_edit'
    )]
    #[Route('/{id}', name: 'edit', methods: ['PATCH'])]
    #[IsGranted("personal", "statement")]
    public function editStatement(
        Request                 $request,
        StatementDTOTransformer $statementDTOTransformer,
        Statement               $statement
    ): Response
    {
        return $this->responseApiForm(
            $request,
            $statement,
            StatementAPIFormType::class,
            $statementDTOTransformer
        );
    }

    /**
     * @param StatementDTOTransformer $statementDTOTransformer
     * @param Statement $statement
     * @return Response
     */
    #[PrepareAuthorizationDocumentation]
    #[OA\Response(
        response: 200,
        description: 'Заявление удалено',
        content: new PrepareSingleDataDocumentation(dto: StatementDTO::class)
    )]
    #[OA\PathParameter(
        name: 'id',
        description: 'Идентификатор заявления',
        required: true,
        schema: new OA\Schema(type: 'integer', minimum: 1),
        example: 5
    )]
    #[OA\Delete(
        description: 'Удалить заявление',
        summary: 'Удалить заявление - api_statement_delete'
    )]
    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    #[IsGranted("personal", "statement")]
    public function deleteStatement(
        StatementDTOTransformer $statementDTOTransformer,
        Statement               $statement
    ): Response
    {
        $this->doctrine->getManager()->remove($statement);
        $this->doctrine->getManager()->flush();

        return $this->prepareSingleDataResponse(
            $statement,
            $statementDTOTransformer
        );
    }
}