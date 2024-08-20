<?php

namespace App\DTO\Response\User;

use DateTimeInterface;
use JMS\Serializer\Annotation\Type;
use OpenApi\Attributes as OA;


class ClientDTO
{
    #[Type("int")]
    #[OA\Property(minimum: 1, nullable: false)]
    public int $id;

    #[Type("string")]
    public ?string $address;

    #[Type('string')]
    public ?string $phoneNumber;

    #[Type("DateTimeInterface<\"Y-m-d\">")]
    public ?DateTimeInterface $birthday;

    #[Type(UserDTO::class)]
    public UserDTO $user;
}