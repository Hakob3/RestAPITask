<?php

namespace App\DTO\Response;

use JMS\Serializer\Annotation\Groups;

class SingleDataDto
{
    /**
     * @var string
     */
    #[Groups(['wrapper'])]
    public mixed $item;
}