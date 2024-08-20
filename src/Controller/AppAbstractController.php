<?php

namespace App\Controller;

use App\DTO\Transformer\AbstractResponseDtoTransformer;
use App\Exception\ApiException;
use App\Service\Form\ValidatorService;
use App\Service\SerializerService;
use Doctrine\Persistence\ManagerRegistry;
use Iterator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

abstract class AppAbstractController extends AbstractController
{
    /**
     * @param UrlGeneratorInterface $urlGenerator
     * @param ManagerRegistry $doctrine
     * @param FormFactoryInterface $formFactory
     * @param ValidatorService $validatorService
     * @param SerializerService $serializerService
     */
    public function __construct(
        protected UrlGeneratorInterface $urlGenerator,
        protected ManagerRegistry       $doctrine,
        protected FormFactoryInterface  $formFactory,
        protected ValidatorService      $validatorService,
        protected SerializerService     $serializerService,
    )
    {
    }

    /** @var string[]|null */
    protected ?array $serializerGroup = [];

    public function prepareJsonResponse(string $jsonData): Response
    {
        $response = new Response();
        $response->setContent($jsonData);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @param Request $request
     * @param object $entity
     * @param string $formType
     * @param AbstractResponseDtoTransformer $dtoTransformer
     * @param callable|null $prePersistCallback
     * @return Response
     * @throws ApiException
     */
    protected function responseApiForm(
        Request                        $request,
        object                         $entity,
        string                         $formType,
        AbstractResponseDtoTransformer $dtoTransformer,
        ?callable                      $prePersistCallback = null
    ): Response
    {
        return $this->prepareSingleDataResponse(
            $this->processForm(
                $request,
                $entity,
                $formType,
                $prePersistCallback
            ),
            $dtoTransformer
        );
    }

    /**
     * @throws ApiException
     */
    public function processForm(
        Request   $request,
        object    $entity,
        string    $formType,
        ?callable $prePersistCallback = null
    ): object
    {
        $form = $this->formFactory->createNamed('', $formType, $entity, ['csrf_protection' => false]);
        if ($request->request->all() === []) {
            $clearMissing = $request->getMethod() !== Request::METHOD_PATCH;
            $jsonContent = json_decode($request->getContent(), true);
            if ($jsonContent !== [] && $jsonContent !== null) {
                $form->submit($jsonContent, $clearMissing);
            } else {
                $form->submit([], $clearMissing);
            }
        } else {
            $form->handleRequest($request);
        }

        if ($form->isSubmitted() && $form->isValid()) {
            if (null !== $prePersistCallback) {
                $prePersistCallback();
            }
            $this->doctrine->getManager()->persist($entity);
            $this->doctrine->getManager()->flush();

            return $entity;
        }

        $errorMessages = $this->validatorService->getFormErrorsMessage($form);

        throw new ApiException(
            message: 'validator error',
            code: ValidatorService::VALIDATOR_CODE,
            payload: $errorMessages->errorMessages
        );
    }

    /**
     * @param object|null $object
     * @param AbstractResponseDtoTransformer $dtoTransformer
     * @return Response
     */
    protected function prepareSingleDataResponse(
        ?object                        $object,
        AbstractResponseDtoTransformer $dtoTransformer
    ): Response
    {
        return $this->prepareJsonResponse(
            $this->serializerService->prepareSingleDataResponse(
                $object,
                $dtoTransformer,
                $this->serializerGroup
            )
        );
    }

    /**
     * @param array|Iterator|null $callableData
     * @param AbstractResponseDtoTransformer $dtoTransformer
     * @return Response
     *
     */
    protected function prepareManyDataResponse(
        array|Iterator|null            $callableData,
        AbstractResponseDtoTransformer $dtoTransformer,
    ): Response
    {
        return $this->prepareJsonResponse(
            $this->serializerService->prepareManyDataResponse(
                $callableData,
                $dtoTransformer,
                $this->serializerGroup
            )
        );
    }
}