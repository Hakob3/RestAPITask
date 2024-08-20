<?php

namespace App\Service\ApiDocumentation;

use Attribute;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use OpenApi\Attributes\JsonContent;

#[Attribute(Attribute::TARGET_METHOD)]
class PrepareManyDataDocumentation extends JsonContent
{
    /**
     * @param string $dto
     * @param array|null $groups
     */
    public function __construct(string $dto, ?array $groups = null)
    {
        parent::__construct(
            properties: [
                new OA\Property(
                    property: "code",
                    type: "integer",
                    example: "200"
                ),
                new OA\Property(
                    property: "items",
                    type: 'array',
                    items: new OA\Items(
                        oneOf: [
                            new OA\Schema(
                                ref: new Model(type: $dto, groups: $groups),
                                type: 'object'
                            )
                        ]
                    )
                ),
                new OA\Property(
                    property: 'count',
                    type: 'int',
                    example: 5
                )
            ],
            type: 'object'
        );
    }
}
