<?php

namespace App\Service\ApiDocumentation;

use Attribute;
use OpenApi\Attributes as OA;

#[Attribute(Attribute::TARGET_METHOD)]
class PrepareAuthorizationDocumentation extends OA\HeaderParameter
{
    public function __construct()
    {
        parent::__construct(
            name: 'Authorization',
            description: 'Токен для авторизации',
            in: 'header',
            required: true,
            schema: new OA\Schema(type: 'string', example: 'Bearer token')
        );
    }
}
