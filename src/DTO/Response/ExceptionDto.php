<?php

namespace App\DTO\Response;

use JMS\Serializer\Annotation\Type;

class ExceptionDto
{
    /**
     * @var string
     */
    #[Type("string")]
    public string $message;

    #[Type("int")]
    public int $code;

    #[Type("array")]
    public ?array $payload;
}