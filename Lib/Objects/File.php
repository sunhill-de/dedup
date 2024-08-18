<?php 

namespace Sunhill\Dedup\Objects;

use Illuminate\Support\Facades\DB;

class File extends DedupObject
{
    
    public $hash;
    
    public $size;
    
    public $mime;
    
    public $dir;
    
    public $name;
    
    public $extension;
    
    public $state = 'regular';
    
    public function scan(string $file)
    {
        if (!file_exists($file)) {
            throw \Exception("File '$file' does not exists");
        }
        
        $this->hash = sha1_file($file);
        $this->size = filesize($file);
        $mime_type = mime_content_type($file);
        $this->mime = new Mime();
        if ($mime = Mime::searchValues($mime_type)) {
            $this->mime->load($mime); 
        } else {
            $this->mime->loadValues($mime_type);
            $this->mime->commit();
        }
        $this->dir = realpath(pathinfo($file,PATHINFO_DIRNAME));
        $this->name = pathinfo($file,PATHINFO_FILENAME);
        $this->extension = pathinfo($file,PATHINFO_EXTENSION);
    }
    
    protected function doUpdateDatabase()
    {
        DB::table('files')->update(
            [
                'hash'=>$this->hash,
                'size'=>$this->size,
                'mime'=>$this->mime->getID(),
                'dir'=>$this->dir,
                'name'=>$this->name,
                'extension'=>$this->extension,
                'state'=>$this->state
            ])->where('id', $this->id);
    }
    
    protected function doInsertDatabase()
    {
        return DB::table('files')->insertGetId(
            [
                'hash'=>$this->hash,
                'size'=>$this->size,
                'mime'=>$this->mime->getID(),
                'dir'=>$this->dir,
                'name'=>$this->name,
                'extension'=>$this->extension,
                'state'=>$this->state
            ]);
    }
    
    protected function getTableName()
    {
        return 'files';
    }
    
    protected function postLoad()
    {
        $mime = new Mime();
        $mime->load($this->mime);
        $this->mime = $mime;
    }
    
    static public function searchValues(string $hash)
    {
        $result =  DB::table('files')->where(['hash'=>$hash])->first();
        if ($result) {
            return $result->id;
        } else {
            return null;
        }
    }
    
    
}