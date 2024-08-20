<?php

namespace App\DTO\Response;

use JMS\Serializer\Annotation\Groups;

class ManyDataDto
{
    /**
     * @var iterable
     */
    #[Groups(['wrapper'])]
    public iterable $items;

    /**
     * @var int
     */
    #[Groups(['wrapper'])]
    public int $count;
}