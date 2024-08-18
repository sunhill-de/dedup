<?php

namespace Sunhill\Dedup\Objects;

use Illuminate\Support\Facades\DB;

abstract class DedupObject
{
    
    public $id = null;
    
    public function getID(): ?int
    {
        return $this->id;
    }
    
    public function commit()
    {
        if ($this->id) {
            $this->updateDatabase();
        } else {
            $this->insertDatabase();
        }
    }
    
    protected function updateDatabase()
    {
        $this->doUpdateDatabase();
    }
    
    abstract protected function doUpdateDatabase();
    
    abstract protected function doInsertDatabase();
    
    abstract protected function getTableName();
    
    protected function insertDatabase()
    {
        $this->doInsertDatabase();
        $this->id = DB::getPdo()->lastInsertId();        
    }

    protected function postLoad()
    {
        
    }
    
    public function load(int $id)
    {
        $entries = DB::table($this->getTableName())->where('id', $id)->first();
        foreach ($entries as $key => $value) {
            $this->$key = $value;
        }
        $this->postLoad();
    }
        
}