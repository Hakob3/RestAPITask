<?php

namespace App\DTO\Transformer;

use App\DTO\Response\SingleDataDto;

class SingleDataTransformer extends AbstractResponseDtoTransformer
{
    /**
     * @param mixed $object
     * @return SingleDataDto
     */
    public function transformFromObject($object): SingleDataDto
    {
        $dto = new SingleDataDto();
        $dto->item = $object;
        return $dto;
    }
}