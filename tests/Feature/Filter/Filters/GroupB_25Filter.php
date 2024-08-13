<?php
namespace Sunhill\Dedup\Tests\Feature\Filter\Filters;

use Sunhill\Dedup\Filter\Filter;

class GroupB_25Filter extends TestFilter
{
    
    static protected $group = 'GroupB';
    
    static protected $priority = 25;
    
    static protected $result = 'SUFFICIENTSTOP';
    
    protected static function initializeConditions()
    {
        static::$conditions = ['condition_25'=>true];
    }
    
    
}