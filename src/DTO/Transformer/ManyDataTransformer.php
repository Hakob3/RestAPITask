<?php

namespace App\DTO\Transformer;

use App\DTO\Response\ManyDataDto;

class ManyDataTransformer extends AbstractResponseDtoTransformer
{
    /**
     * @param iterable $object
     * @return ManyDataDto
     */
    public function transformFromObject(mixed $object): ManyDataDto
    {
        $dto = new ManyDataDto();
        $dto->items = $object;
        $dto->count = count($object);
        return $dto;
    }
}