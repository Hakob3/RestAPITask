<?php

namespace App\DTO\Transformer;

abstract class AbstractResponseDtoTransformer implements ResponseDtoTransformerInterface
{
    /**
     * @param iterable $objects
     * @return iterable
     */
    public function transformFromObjects(iterable $objects): iterable
    {
        $data = [];
        foreach ($objects as $object) {
            $data[] = $this->transformFromObject($object);
        }
        return $data;
    }
}