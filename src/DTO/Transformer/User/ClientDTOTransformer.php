<?php

namespace App\DTO\Transformer\User;

use App\DTO\Response\User\ClientDTO;
use App\DTO\Transformer\AbstractResponseDtoTransformer;
use App\Entity\Client;

class ClientDTOTransformer extends AbstractResponseDtoTransformer
{
    /**
     * @param UserDTOTransformer $userDTOTransformer
     */
    public function __construct(private readonly UserDTOTransformer $userDTOTransformer)
    {
    }

    /**
     * @param Client $object
     * @return ClientDTO
     */
    public function transformFromObject($object): ClientDTO
    {
        $dto = new ClientDTO();

        $dto->id = $object->getId();
        $dto->address = $object->getAddress();
        $dto->phoneNumber = $object->getPhoneNumber();
        $dto->birthday = $object->getBirthday();
        $dto->user = $this->userDTOTransformer->transformFromObject($object->getUser());

        return $dto;
    }
}
