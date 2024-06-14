<?php

namespace Sunhill\Dedup;

use Illuminate\Support\Str;

class File
{

    protected $file = '';
    
    protected $file_size = 0;
    
    protected $short_hash = '';
    
    protected $long_hash = null;
    
    protected $mime_group = '';
    
    protected $mime_subgroup = '';
    
    protected $state;

    protected $id = 0;
    
    protected $options = [];
    
    protected function getMethod(string $option, array $parameters)
    {
        if (!isset($this->options[$option])) {
            throw new \Exception("Access of unknown option '$option'");
        }
        $result = $this->options[$option];
        if (is_scalar($result)) {
            return $result;
        }
        if (is_callable($result)) {
            return $result($this,...$parameters);
        }
    }
    
    protected function setMethod(string $option, $value = true)
    {
        $this->options[$option] = $value;
    }
    
    public function __call($method, $parameters)
    {
        if (Str::startsWith($method,'get_')) {
            return $this->getMethod(substr($method,4),$parameters);
        }
        if (Str::startsWith($method,'set_')) {
            if (empty($parameters)) {
                return $this->setMethod(substr($method,4),true);                
            }
            return $this->setMethod(substr($method,4),$parameters[0]);            
        }
        throw new \Exception("Unknown method '$method'");
    }
    
    public function readFile(string $path)
    {
        $this->file = $path;
        $this->file_size = filesize($path);
        list($this->mime_group,$this->mime_subgroup) = explode('/', mime_content_type($this->file));
    }
    
    public function filePath(): string
    {
        return $this->file;
    }
    
    public function longHash(): string
    {
        if (empty($this->long_hash)) {
            $this->long_hash = sha1_file($this->file);
        }
        return $this->long_hash;
    }
    
    public function shortHash(): string
    {
        if (empty($this->short_hash)) {
            $fp = fopen($this->file, 'r');
            $data = fread($fp, 40);
            $this->short_hash = sha1($data);
            fclose($fp);            
        }
        return $this->short_hash;
    }
    
    public function size(): int
    {
        return filesize($this->file);
    }

    public function setState(string $state)
    {
        $this->state = $state;
    }
    
    public function getState(): string
    {
        return $this->state;
    }
    
    public function getMimeGroup(): string
    {
        return $this->mime_group;
    }
    
    public function getMimeSubgroup(): string
    {
        return $this->mime_subgroup;
    }
    
    public function setID(int $id)
    {
        $this->id = $id;
    }
    
    public function getID(): int
    {
        return $this->id;
    }
}