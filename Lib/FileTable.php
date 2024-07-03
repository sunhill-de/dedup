<?php

namespace Sunhill\Dedup;

use Illuminate\Support\Facades\DB;

class FileTable    
{

    protected function hasShortHash(File $file)
    {
        $short_hash = $file->shortHash();
        $query = DB::table('hashtable')->where('short_hash',$short_hash)->get();
        return $query;
    }
    
    protected function recalcEntry($entry)
    {
        $file = new File($entry->file_path);
        DB::table('hashtable')->where('id',$entry->id)->update(['long_hash'=>$file->longHash()]);
    }
    
    protected function checkForRecalc($query)
    {
        foreach ($query as $entry) {
            if (is_null($entry->long_hash)) {
                $this->recalcEntry($entry);
            }
        }
    }
    
    protected function hasLongHash(File $file)
    {
        $long_hash = $file->longHash();
        $query = DB::table('hashtable')->where('long_hash', $long_hash)->first();
        return !empty($query);
    }
    
    public function getFileWithHash(string $hash): string
    {
        $query = DB::table('hashtable')->where('long_hash', $hash)->first();
        return $query->file_path;
    }
    
    public function hasFile(File $file): bool
    {
        $query = $this->hasShortHash($file);
        
        if (!$query->isEmpty()) {
            $this->checkForRecalc($query);
        } else {
            return false;
        }
        return $this->hasLongHash($file);
    }
    
}