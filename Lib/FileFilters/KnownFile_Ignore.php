<?php

namespace Sunhill\Dedup\FileFilters;

use Sunhill\Dedup\Filter\Filter;

class KnownFile_Ignore extends FileFilter
{
    
    static protected $priority = 10;
    
    protected static function initializeConditions()
    {
        static::$conditions = [
            'handle_known_file'=>'ignore',
            'is_known_file'=>true,
            'is_new_file'=>false,
        ];
    }
    
    public function execute(): string
    {
        $this->container->setCondition('message', 'Ignoring already known file');
        return 'SUFFICIENTSTOP';
    }
    
}