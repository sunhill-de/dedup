<?php
namespace Sunhill\Dedup\Tests\Feature\Filter\Filters;

use Sunhill\Dedup\Filter\Filter;

class GroupB_60Filter extends TestFilter
{
    
    static protected $group = 'GroupB';
    
    static protected $priority = 60;
    
    static protected $result = 'CONTINUE';
    
    protected static function initializeConditions()
    {
        static::$conditions = ['condition_60'=>true,'additional'=>'ABC'];
    }
    
    
}