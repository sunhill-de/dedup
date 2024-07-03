<?php

namespace Sunhill\Dedup;

use Illuminate\Support\Str;

class Scanner 
{
    
    protected $directory = '';
    
    public function setDirectory(string $directory): self
    {
        if (Str::startsWith($directory,'/')) {
            $this->directory = $directory;            
        } else {
            $this->directory = getcwd().'/'.$directory;
        }
        return $this;
    }
    
    protected $recursive = false;
    
    public function setRecursive(): self
    {
        $this->recursive = true;
        return $this;
    }
    
    protected $new_file_action = 'ignore';
    
    public function setNewFileAction(string $action): self
    {
        $this->new_file_action = $action;
        return $this;
    }
    
    protected $new_file_destination = '';
    
    public function setNewFileDestination(string $destination): self
    {
        $this->new_file_destination = $destination;
        return $this;
    }
    
    protected $known_file_action = 'ignore';
    
    public function setknownFileAction(string $action): self
    {
        $this->known_file_action = $action;
        return $this;
    }
    
    protected $known_file_destination = '';
    
    public function setknownFileDestination(string $destination): self
    {
        $this->known_file_destination = $destination;
        return $this;
    }
    
    protected $cache = true;
    
    public function setNoCache(): self
    {
        $this->cache = false;
        return $this;
    }
    
    protected $dry_run = false;
    
    public function setDryRun(): self
    {
        $this->dry_run = true;
        return $this;
    }

    protected $remove_empty_dirs = false;
    
    public function setRemoveEmptyDirs(): self
    {
        $this->remove_empty_dirs = true;
        return $this;
    }
    
    protected $follow_links = false;
    
    public function setFollowLinks(): self
    {
        $this->follow_links = true;
        return $this;
    }
    
    protected $prefix_type = false;
    
    public function setPrefixType(): self
    {
        $this->prefix_type = true;
        return $this;
    }
    
    protected $ignore_prefix = '';
    
    public function setIgnorePrefix(string $prefix): self
    {
        $this->ignore_prefix = $prefix;
    }
    
    protected function checkDirExistance()
    {
        if (!file_exists($this->directory)) {
            throw new \Exception('Directory not found: '.$this->directory);
        }
    }
    
    protected function collectDir(string $directory): array
    {
        $entries = [];
        $dir = dir($directory);
        while (($entry = $dir->read()) !== false) {
            if (($entry == '.') || ($entry == '..')) {
                continue;
            }
            $entries[] = $directory.'/'.$entry;
        }
        return $entries;
    }
    
    protected function handleLink(string $link)
    {
        
    }

    public function buildDestination(string $file, string $destination, string $ignore_prefix, string $prefix_type)
    {
        if (!empty($ignore_prefix)) {
            $file = Str::remove($ignore_prefix,$file);
        }
        if (Str::startsWith($file,'/')) {
            $file = substr($file,1);
        }
        $prefix = empty($prefix_type)?'':$prefix_type.'/';
        return $destination.$prefix.$file;
    }

    protected function getType(File $file)
    {
        return Str::before(mime_content_type($file->filePath()),'/');    
    }
    
    protected function createParentDir(string $file)
    {
        $parts = explode('/', $file);
        array_pop($parts);
        $file = implode('/', $file);
        
        if (file_exists($file) && is_dir($file)) {
            return;
        }
        if ($this->dry_run) {
            $this->message('Dry run: Create directory:'.$file);            
        } else {
            $this->message('Create directory:'.$file);
            mkdir($file);
        }
    }
    
    protected function moveFile(File $file, string $destination)
    {
        $this->createParentDir($destination);
        if ($this->dry_run) {
            $this->message('Dry run: Moving '.$file->getPath().">".$destination);
        } else {
            $this->message('Moving '.$file->getPath().">".$destination);
            rename($file->getPath(),$destination);
        }
    }
    
    protected function deleteFile(File $file)
    {
        if ($this->dry_run) {
            $this->message('Dry run: Deleting '.$file->getPath());
        } else {
            $this->message('Deleting '.$file->getPath());
            unlink($file->getPath());
        }        
    }
    
    protected function linkFile(File $file)
    {
        $file_table = new FileTable();
        $destination = $file_table->getFileWithHash($file->longHash());
        $this->createParentDir($destination);
        $this->deleteFile($file);
        if ($this->dry_run) {
            $this->message('Dry run: Linking '.$file->getPath()."=>".$destination);            
        } else {
            $this->message('Linking '.$file->getPath()."=>".$destination);
            symlink($destination, $file->getPath);
        }
    }
    
    protected function handleKnownFile(File $file)
    {
        switch ($this->known_file_action) {
            case 'move':
                $this->moveFile($file, $this->buildDestination($file->filePath(), $this->known_file_destination, $this->ignore_prefix, $this->prefix_type?$this->getType($file):''));
                break;
            case 'link':
                $this->linkFile($file);
                break;
            case 'delete':
                $this->deleteFile($file);
                break;
            case 'report':
                break;
            case 'ignore':
                $this->message("Ignoring: ".$file->getPath());
                break;
        }
    }
    
    protected function handleNewFile(File $file)
    {
        switch ($this->new_file_action) {
            case 'report':
                break;
            case 'move':
                break;
            case 'ignore':
                break;
        }
    }
    
    protected function handleFile(string $file)
    {
         $file = new File($file);
         $file_table = new FileTable();
         if ($file_table->hasFile($file)) {
             $this->handleKnownFile($file);
         } else {
             $this->handleNewFile($file);
         }
    }
    
    protected function removeEmptyDir(string $dir)
    {
        if ($this->dry_run) {
            $this->message('Dry run: Removing directory: '.$dir);            
        } else {
            $this->message('Removing directory: '.$dir);
            rmdir($dir);
        }
    }
    
    protected function handleDir(string $dir)
    {
        $this->message("Entering dir: ".$dir);
        $entries = $this->collectDir($dir);
        foreach ($entries as $entry) {
            if (is_dir($entry)) {
                if ($this->recursive) {
                    $this->handleDir($entry);
                }
            } else if (is_link($entry)) {
                if ($this->follow_links) {
                    $this->handleLink($entry);
                }
            } else if (is_file($entry)) {
                $this->handleFile($entry);
            } 
        }
        $entries = $this->collectDir($dir);
        if (empty($entries)) {
            $this->message("Detected empty dir: ".$dir);
            if ($this->remove_empty_dirs) {
                $this->removeEmptyDir($dir);
            }
        }
    }
    
    public function message(string $message)
    {
        $out = new \Symfony\Component\Console\Output\ConsoleOutput();
        $out->writeln($message);
    }
    
    public function run()
    {
        $this->checkDirExistance();
        $this->handleDir($this->directory);
    }
    
}