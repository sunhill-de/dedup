<?php

use Tests\TestCase;
use Sunhill\Dedup\File;
use Sunhill\Dedup\Filter;

uses(TestCase::class);

test('Filter match', function() 
{
    $file = \Mockery::mock(File::class);
    $file->shouldReceive('get_something')->once()->andReturn(true);
    
    $test = new Filter();
    $test->setConditions(['something'=>true]);
    $test->setTarget($file);
    
    expect($test->matches())->toBe(true);
});

test('Filter match many', function()
{
    $file = \Mockery::mock(File::class);
    $file->shouldReceive('get_something')->once()->andReturn(true);
    $file->shouldReceive('get_somethingelse')->once()->andReturn('ABC');
    
    $test = new Filter();
    $test->setConditions(['something'=>true,'somethingelse'=>'ABC']);
    $test->setTarget($file);
    
    expect($test->matches())->toBe(true);
});

test('Filter fails', function()
{
    $file = \Mockery::mock(File::class);
    $file->shouldReceive('get_something')->once()->andReturn(false);
    
    $test = new Filter();
    $test->setConditions(['something'=>true]);
    $test->setTarget($file);
    
    expect($test->matches())->toBe(false);    
});

test('Filter fails many', function()
{
    $file = \Mockery::mock(File::class);
    $file->shouldReceive('get_something')->once()->andReturn(true);
    $file->shouldReceive('get_somethingelse')->once()->andReturn('ABC');
    
    $test = new Filter();
    $test->setConditions(['something'=>true,'somethingelse'=>'DEF']);
    $test->setTarget($file);
    
    expect($test->matches())->toBe(false);
});

