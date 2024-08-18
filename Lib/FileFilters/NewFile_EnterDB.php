<?php

namespace Sunhill\Dedup\FileFilters;

class NewFile_EnterDB extends FileFilter
{
    
    static protected $priority = 5;
    
    protected static function initializeConditions()
    {
        static::$conditions = [
            'cache'=>true,
            'is_known_file'=>false,
            'is_new_file'=>true,
        ];
    }
    
    public function execute(): string
    {
        $this->message('Add new file to database cache');
        return 'CONTINUE';
    }
    
}