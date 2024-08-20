<?php

namespace App\Service\ApiDocumentation;

use Attribute;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;

#[Attribute(Attribute::TARGET_METHOD)]
class PrepareJsonRequestDocumentation extends OA\MediaType
{
    /**
     * @param string $formType
     */
    public function __construct(string $formType)
    {
        parent::__construct(
            mediaType: "application/json",
            schema: new OA\Schema(
                ref: new Model(type: $formType),
            )
        );
    }
}
