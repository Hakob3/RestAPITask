<?php

namespace App\Service;

use App\DTO\Transformer\AbstractResponseDtoTransformer;
use App\DTO\Transformer\ManyDataTransformer;
use App\DTO\Transformer\SingleDataTransformer;
use JMS\Serializer\Handler\HandlerRegistry;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class SerializerService
{
    private array $customSerializerHandlers = [];

    /**
     * @return Serializer
     */
    public function createSerializer(): Serializer
    {
        $serializer = SerializerBuilder::create()
            ->addDefaultHandlers();

        /** @var SubscribingHandlerInterface $handler */
        foreach ($this->customSerializerHandlers as $handler) {
            $serializer
                ->configureHandlers(
                    function (HandlerRegistry $registry) use ($handler) {
                        $registry->registerSubscribingHandler($handler);
                    }
                );
        }

        return $serializer->build();
    }

    /**
     * @param mixed $dto
     * @param array|null $serializerGroup
     * @return string
     */
    public function serializeToJson(
        mixed  $dto,
        ?array $serializerGroup = []
    ): string
    {
        if ($serializerGroup !== null && count($serializerGroup) !== 0) {
            $context = SerializationContext::create()->setGroups($serializerGroup);
        }

        return $this->createSerializer()
            ->serialize($dto, 'json', $context ?? null);
    }

    /**
     * @param object $data
     * @param AbstractResponseDtoTransformer $dtoTransformer
     * @param array $serializerGroup
     * @return string
     */
    public function prepareSingleDataResponse(
        ?object                        $data,
        AbstractResponseDtoTransformer $dtoTransformer,
        array                          $serializerGroup = []
    ): string
    {
        if ($data === null) {
            throw new NotFoundHttpException(message: 'Not found');
        }
        $dataDto = $dtoTransformer->transformFromObject($data);
        $dto = (new SingleDataTransformer())->transformFromObject($dataDto);

        if (count($serializerGroup) !== 0) {
            $serializerGroup[] = 'wrapper';
        }

        return $this->serializeToJson($dto, $serializerGroup);
    }

    /**
     * @param array|\Iterator|null $data
     * @param AbstractResponseDtoTransformer $dtoTransformer
     * @param array $serializerGroup
     * @return string
     */
    public function prepareManyDataResponse(
        array|\Iterator|null           $data,
        AbstractResponseDtoTransformer $dtoTransformer,
        array                          $serializerGroup = []
    ): string
    {
        $dataDto = $dtoTransformer->transformFromObjects($data);
        $dto = (new ManyDataTransformer())->transformFromObject($dataDto);

        if (count($serializerGroup) !== 0) {
            $serializerGroup[] = 'wrapper';
        }

        return $this->serializeToJson($dto, $serializerGroup);
    }
}