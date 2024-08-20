<?php

namespace App\Exception;

use Throwable;

class ApiException extends \Exception
{
    /**
     * @var array|null
     */
    public ?array $payload;
    /**
     * ApiException constructor.
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     * @param array|null $payload
     */
    public function __construct(string $message = "", int $code = 0, Throwable $previous = null, array $payload = null)
    {
        $this->payload = $payload;
        parent::__construct($message, $code, $previous);
    }
}