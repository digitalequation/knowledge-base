<?php

namespace DigitalEquation\KnowledgeBase\Exceptions;

class KnowledgeBaseJsonException extends \RuntimeException
{
    public function __construct(string $message, $code = 400)
    {
        parent::__construct($message, $code);
    }
}