<?php

namespace App\DTO\Transformer;

interface ResponseDtoTransformerInterface
{
    /**
     * @param $object
     * @return mixed
     */
    public function transformFromObject($object): mixed;

    /**
     * @param iterable $objects
     * @return iterable
     */
    public function transformFromObjects(iterable $objects): iterable;
}