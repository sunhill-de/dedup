<?php

use Sunhill\Dedup\File;
use Illuminate\Support\Facades\DB;
use Sunhill\Dedup\FileTable;
use Tests\TestCase;
use Database\Seeders\HashTableSeeder;

uses(TestCase::class);

test('File table with file not in database', function()
{
    $file = \Mockery::mock(File::class);
    $file->shouldReceive('shortHash')->once()->andReturn('C');
    $file->shouldReceive('longHash')->never();

    $this->artisan('migrate:fresh');
    $this->seed(HashTableSeeder::class);
   // $this->artisan('db:seed');
    
    $test = new FileTable();
    expect($test->hasFile($file))->toBe(false);
});

test('File table with short hash in database but not long hash no recalc', function()
{
    $file = \Mockery::mock(File::class);
    $file->shouldReceive('shortHash')->once()->andReturn('B');
    $file->shouldReceive('longHash')->once()->andReturn('BA');

    $test = new FileTable();

    expect($test->hasFile($file))->toBe(false);
}); 

test('File table with both hashes in database', function()
{
    $file = \Mockery::mock(File::class);
    $file->shouldReceive('shortHash')->once()->andReturn('B');
    $file->shouldReceive('longHash')->once()->andReturn('BB');
        
    $test = new FileTable();
    expect($test->hasFile($file))->toBe(true);    
});

test('Add file', function() 
{
    
});