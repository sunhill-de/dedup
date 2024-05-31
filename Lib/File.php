<?php

namespace Sunhill\Dedup;

class File
{
    
    protected $file;
    
    public function __construct(string $file)
    {
        $this->file = $file;    
    }
    
    public function longHash(): string
    {
        return sha1_file($this->file);
    }
    
    public function shortHash(): string
    {
        $fp = fopen($this->file, 'r');
        $data = fread($fp, 40);
        fclose($fp);
        return sha1($data);
    }
    
    public function size(): int
    {
        return filesize($this->file);
    }
    
}