<?php

use Tests\TestCase;
use Sunhill\Dedup\File;
use Sunhill\Dedup\Filter;

uses(TestCase::class);

test('Filter match', function() 
{
    $file = \Mockery::mock(File::class);
    $file->shouldReceive('getCondition')->once()->with('something')->andReturn(true);
    
    $test = new Filter();
    $test->setConditions(['something'=>true]);
    $test->setTarget($file);
    
    expect($test->matches())->toBe(true);
});

test('Filter match many', function()
{
    $file = \Mockery::mock(File::class);
    $file->shouldReceive('getCondition')->once()->with('something')->andReturn(true);
    $file->shouldReceive('getCondition')->once()->with('somethingelse')->andReturn('ABC');
    
    $test = new Filter();
    $test->setConditions(['something'=>true,'somethingelse'=>'ABC']);
    $test->setTarget($file);
    
    expect($test->matches())->toBe(true);
});

test('Filter fails', function()
{
    $file = \Mockery::mock(File::class);
    $file->shouldReceive('getCondition')->once()->with('something')->andReturn(false);
    
    $test = new Filter();
    $test->setConditions(['something'=>true]);
    $test->setTarget($file);
    
    expect($test->matches())->toBe(false);    
});

test('Filter fails many', function()
{
    $file = \Mockery::mock(File::class);
    $file->shouldReceive('getCondition')->once()->with('something')->andReturn(true);
    $file->shouldReceive('getCondition')->once()->with('somethingelse')->andReturn('ABC');
    
    $test = new Filter();
    $test->setConditions(['something'=>true,'somethingelse'=>'DEF']);
    $test->setTarget($file);
    
    expect($test->matches())->toBe(false);
});

test('static access works', function()
{
   $test = new Filter();
   expect($test->getGroup())->toBe('');
   expect($test->getPriority())->toBe(50);
});