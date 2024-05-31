<?php

use Sunhill\Dedup\File;
use Illuminate\Support\Facades\DB;
use Sunhill\Dedup\FileTable;

test('File table with file not in database', function()
{
    $file = \Mockery::mock(File::class);
    $file->shouldReceive('shortHash')->once()->andReturn('A');
    $file->shouldReceive('longHash')->never();
    
    $dummyquery = \Mockery::mock(\StdClass::class);
    $dummyquery->shouldReceive('where')->with('short_hash','A')->andReturn($dummyquery);
    $dummyquery->shouldReceive('get')->andReturn(null);
    
    DB::shouldReceive('table')->once()->with('hashtable')->andReturn($dummyquery);

    $test = new FileTable();
    expect($test->hasFile($file))->toBe(false);
});

test('File table with short hash in database but not long hash no recalc', function()
{
    $file = \Mockery::mock(File::class);
    $file->shouldReceive('shortHash')->once()->andReturn('A');
    $file->shouldReceive('longHash')->once()->andReturn('B');

    $entry = new \stdClass();
    $entry->id = 1;
    $entry->short_hash = 'A';
    $entry->long_hash = 'C';
    
    $dummyquery = \Mockery::mock(\StdClass::class);
    $dummyquery->shouldReceive('where')->once()->with('short_hash','A')->andReturn($dummyquery);
    $dummyquery->shouldReceive('where')->once()->with('long_hash','B')->andReturn($dummyquery);
    $dummyquery->shouldReceive('get')->andReturn([$entry]);
    $dummyquery->shouldReceive('first')->andReturn(null);

    DB::shouldReceive('table')->with('hashtable')->andReturn($dummyquery);
    
    $test = new FileTable();
    expect($test->hasFile($file))->toBe(false);
});

test('File table with both hashes in database', function()
{
    $file = \Mockery::mock(File::class);
    $file->shouldReceive('shortHash')->once()->andReturn('A');
    $file->shouldReceive('longHash')->once()->andReturn('B');
    
    $dummyquery = \Mockery::mock(\StdClass::class);
    $dummyquery->shouldReceive('where')->once()->with('short_hash','A')->andReturn($dummyquery);
    $dummyquery->shouldReceive('where')->once()->with('long_hash','B')->andReturn($dummyquery);
    $dummyquery->shouldReceive('get')->andReturn('A');
    $dummyquery->shouldReceive('first')->andReturn('B');
    
    DB::shouldReceive('table')->twice()->with('hashtable')->andReturn($dummyquery);
    
    $test = new FileTable();
    expect($test->hasFile($file))->toBe(true);    
});

test('Add file', function() 
{
    
});