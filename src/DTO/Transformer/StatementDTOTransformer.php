<?php

namespace App\DTO\Transformer;

use App\DTO\Response\StatementDTO;
use App\DTO\Transformer\User\ClientDTOTransformer;
use App\Entity\Statement;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class StatementDTOTransformer extends AbstractResponseDtoTransformer
{
    /**
     * @param ClientDTOTransformer $clientDTOTransformer
     * @param UploaderHelper $uploaderHelper
     */
    public function __construct(
        private readonly ClientDTOTransformer $clientDTOTransformer,
        private readonly UploaderHelper       $uploaderHelper,
    )
    {
    }

    /**
     * @param Statement $object
     * @return StatementDTO
     */
    public function transformFromObject($object): StatementDTO
    {
        $dto = new StatementDTO();

        $dto->id = $object->getId();
        $dto->title = $object->getTitle();
        $dto->content = $object->getContent();
        $dto->number = $object->getNumber();
        $dto->client = $this->clientDTOTransformer->transformFromObject($object->getClient());
        $dto->attachmentFile = $this->uploaderHelper->asset($object, 'attachmentFile');
        $dto->createdAt = $object->getCreatedAt();
        $dto->updatedAt = $object->getUpdatedAt();

        return $dto;
    }
}
