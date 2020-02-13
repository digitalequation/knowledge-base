<?php

namespace DigitalEquation\KnowledgeBase;

use Illuminate\Support\Facades\Facade;

/**
 * @see \DigitalEquation\KnowledgeBase\KnowledgeBase
 */
class KnowledgeBaseFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'knowledge-base';
    }
}
