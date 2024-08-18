<?php 

namespace Sunhill\Dedup\Objects;

use Illuminate\Support\Facades\DB;

class Mime extends DedupObject
{
    
    public $main = '';
    
    public $sub = '';
    
    protected function doUpdateDatabase()
    {
        DB::table('mimes')->update(['main'=>$this->main,'sub'=>$this->sub])->where('id', $this->id);   
    }
    
    protected function doInsertDatabase()
    {
        return DB::table('mimes')->insertGetId(['main'=>$this->main,'sub'=>$this->sub]);        
    }
 
    public function loadValues(string $mime_string)
    {
        [$this->main,$this->sub] = explode('/', $mime_string);
    }
    
    static public function searchValues(string $main_or_mime, ?string $sub = null)
    {
        if (is_null($sub)) {
            [$main,$sub] = explode('/', $main_or_mime);
        } else {
            $main = $main_or_mime;
        }
        return DB::table('mimes')->where(['main'=>$main,'sub'=>$sub])->first();        
    }
}