<?php

namespace Sunhill\Dedup\Tests\Feature\Filter\Filters;

use Sunhill\Dedup\Filter\Filter;

class TestFilter extends Filter
{
    
    static protected $group = 'GroupB';
    
    static protected $priority = 10;
    
    static protected $result = 'CONTINUE';
    
    public function execute(): string
    {
        $this->container->setCondition('groupB',$this->container->getCondition('groupB').'_'.static::$priority);
        return static::$result;
    }
    
}