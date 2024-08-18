<?php

namespace Sunhill\Dedup\Tests\Unit\Objects;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Database\Seeders\FilesTableSeeder;
use Sunhill\Dedup\Objects\File;
use Sunhill\Dedup\Objects\Mime;
use Database\Seeders\MimesTableSeeder;

uses(TestCase::class);
uses(RefreshDatabase::class);

test('Scan file', function()
{
    $this->seed([MimesTableSeeder::class,FilesTableSeeder::class]);
    
    $file = new File();
    $file->scan(dirname(__FILE__).'/../../SampleFiles/scan/A.txt');
    expect($file->hash)->toBe('6dcd4ce23d88e2ee9568ba546c007c63d9131c1b');
    expect($file->size)->toBe(1);
    expect($file->mime->main)->toBe('application');
    expect(is_dir($file->dir))->toBe(true);
    expect($file->name)->toBe('A');
    expect($file->extension)->toBe('txt');
});

test('Find entry with success', function()
{
   $this->seed(FilesTableSeeder::class);
   
   $file = File::searchValues('6dcd4ce23d88e2ee9568ba546c007c63d9131c1b');
   
   expect($file)->toBe(1);
});

test('Find entry with failure', function()
{
    $this->seed(FilesTableSeeder::class);
    
    $file = File::searchValues('6dcd4ce23d88e2ee9568ba546c007c63d9131c1f');
    
    expect($file)->toBe(null);
});

test('Add entry', function() 
{
    $this->seed([MimesTableSeeder::class,FilesTableSeeder::class]);
    
    $file = new File();
    $file->hash = 'abc';
    $file->size = 10;;
    $file->mime = new Mime();
    $file->mime->id = 2;
    $file->dir = '/some/dir/to/file';
    $file->name = 'testfile';
    $file->extension = 'txt';
    
    
    $file->commit();
    
    $this->assertDatabaseHas('files',['hash'=>'abc','size'=>10]);
    expect($file->getID() > 0)->toBe(true);
});

test('Load entry', function() 
{
    $this->seed([MimesTableSeeder::class,FilesTableSeeder::class]);
    
    $file = new File();
    $file->load(1);
    
    expect($file->hash)->toBe('6dcd4ce23d88e2ee9568ba546c007c63d9131c1b');
    expect($file->size)->toBe(1);
    expect($file->mime->id)->toBe(2);
});

