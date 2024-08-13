<?php
namespace Sunhill\Dedup\Tests\Feature\Filter\Filters;

use Sunhill\Dedup\Filter\Filter;

class GroupB_20Filter extends TestFilter
{
    
    static protected $group = 'GroupB';
    
    static protected $priority = 20;
    
    static protected $result = 'STOP';
    
    protected static function initializeConditions()
    {
        static::$conditions = ['condition_20'=>true];
    }
    
    
}