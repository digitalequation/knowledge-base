<?php

namespace DigitalEquation\KnowledgeBase\Exceptions;

use Exception;

class KnowledgeBaseHttpException extends Exception
{
    public function __construct(string $message, $code = 400)
    {
        parent::__construct($message, $code);
    }
}
