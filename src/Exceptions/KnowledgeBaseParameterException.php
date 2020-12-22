<?php

namespace DigitalEquation\KnowledgeBase\Exceptions;

use Exception;

class KnowledgeBaseParameterException extends Exception
{
    public function __construct(string $message, $code = 400)
    {
        parent::__construct($message, $code);
    }
}
