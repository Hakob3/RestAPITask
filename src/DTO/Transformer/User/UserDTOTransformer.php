<?php

namespace App\DTO\Transformer\User;

use App\DTO\Response\User\UserDTO;
use App\DTO\Transformer\AbstractResponseDtoTransformer;
use App\Entity\User;

class UserDTOTransformer extends AbstractResponseDtoTransformer
{
    /**
     * @param User $object
     * @return UserDTO
     */
    public function transformFromObject($object): UserDTO
    {
        $dto = new UserDTO();
        $dto->id = $object->getId();
        $dto->email = $object->getEmail();

        return $dto;
    }
}
