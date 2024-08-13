<?php

namespace Sunhill\Dedup\FileFilters;

use Sunhill\Dedup\Filter\Filter;

class NewFile_Ignore extends FileFilter
{
    
    static protected $priority = 10;
    
    protected static function initializeConditions()
    {
        static::$conditions = [
            'handle_new_file'=>'ignore',
            'is_known_file'=>false,
            'is_new_file'=>true,
        ];
    }
    
    public function execute(): string
    {
        return 'SUFFICIENTSTOP';
    }
    
}