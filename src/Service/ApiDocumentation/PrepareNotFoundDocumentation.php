<?php

namespace App\Service\ApiDocumentation;

use Attribute;
use OpenApi\Attributes as OA;
use OpenApi\Attributes\JsonContent;

#[Attribute(Attribute::TARGET_METHOD)]
class PrepareNotFoundDocumentation extends JsonContent
{
    public function __construct()
    {
        parent::__construct(
            properties: [
                new OA\Property(
                    property: "message",
                    type: 'string',
                ),
                new OA\Property(
                    property: "code",
                    type: 'int',
                    example: 404
                ),
            ],
            type: 'object',
        );
    }
}
