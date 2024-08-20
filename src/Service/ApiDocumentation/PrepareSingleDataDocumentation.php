<?php

namespace App\Service\ApiDocumentation;

use Attribute;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use OpenApi\Attributes\JsonContent;

#[Attribute(Attribute::TARGET_METHOD)]
class PrepareSingleDataDocumentation extends JsonContent
{
    /**
     * @param string $dto
     * @param array|null $groups
     * @param array|null $additionalProperties
     */
    public function __construct(string $dto, ?array $groups = null, ?array $additionalProperties = [])
    {
        parent::__construct(
            properties: array_merge([
                new OA\Property(
                    property: "code",
                    type: "integer",
                    example: "200"
                ),
                new OA\Property(
                    property: "item",
                    ref: new Model(type: $dto, groups: $groups),
                    type: 'object'
                )
            ],
                $additionalProperties),
            type: 'object'
        );
    }
}
