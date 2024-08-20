<?php

namespace App\Security\Voter;

interface AppVoterInterface
{
    /** @var string  */
    public const PERSONAL = "personal";

    /** @var string  */
    public const CREATE = "create";


    /** @var string[] */
    public const OPERATIONS = [
        self::PERSONAL,
        self::CREATE
    ];
}