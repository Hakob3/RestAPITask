<?php

namespace App\DTO\Response;

use App\DTO\Response\User\ClientDTO;
use DateTimeInterface;
use JMS\Serializer\Annotation\Type;
use OpenApi\Attributes as OA;

class StatementDTO
{
    #[Type("int")]
    #[OA\Property(minimum: 1, nullable: false)]
    public ?int $id = null;

    #[Type("string")]
    #[OA\Property(nullable: false)]
    public ?string $title = null;

    #[Type("string")]
    public ?string $content = null;

    #[Type("int")]
    #[OA\Property(nullable: false)]
    public ?int $number = null;

    #[Type(ClientDTO::class)]
    #[OA\Property(nullable: false)]
    public ?ClientDTO $client = null;

    #[Type('string')]
    #[OA\Property(description: 'File uri', type: 'URI', example: "/path/to/file.ext", nullable: true)]
    public ?string $attachmentFile = null;

    #[Type("DateTimeInterface<\"Y-m-d H:i:s\">")]
    #[OA\Property(nullable: false)]
    public ?DateTimeInterface $createdAt = null;

    #[Type("DateTimeInterface<\"Y-m-d H:i:s\">")]
    #[OA\Property(nullable: false)]
    public ?DateTimeInterface $updatedAt = null;
}