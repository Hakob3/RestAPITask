<?php

namespace App\DTO\Response\User;

use JMS\Serializer\Annotation\Type;
use OpenApi\Attributes as OA;

class UserDTO
{
    #[Type("int")]
    #[OA\Property(minimum: 1, nullable: false)]
    public int $id;

    #[Type("string")]
    public string $email;
}