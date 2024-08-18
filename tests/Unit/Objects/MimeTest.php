<?php

namespace Sunhill\Dedup\Tests\Unit\Objects;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Database\Seeders\MimesTableSeeder;
use Sunhill\Dedup\Objects\Mime;
use Tests\TestCase;

uses(TestCase::class);
uses(RefreshDatabase::class);

test('Find entry with success (combined mime)', function()
{
   $this->seed(MimesTableSeeder::class);
   
   $mime = Mime::searchValues('audio/mpeg');
   
   expect($mime)->toBe(1);
});

test('Find entry with success (single mime)', function()
{
    $this->seed(MimesTableSeeder::class);
    
    $mime = Mime::searchValues('audio','mpeg');
    
    expect($mime)->toBe(1);
});

test('Find entry with failure', function()
{
    $this->seed(MimesTableSeeder::class);
    
    $mime = Mime::searchValues('image/jpeg');
    
    expect($mime)->toBe(null);
});

test('Add entry', function() 
{
    $this->seed(MimesTableSeeder::class);
    
    $mime = new Mime();
    $mime->main = 'image';
    $mime->sub = 'jpeg';
    
    $mime->commit();
    
    $this->assertDatabaseHas('mimes',['main'=>'image','sub'=>'jpeg']);
    expect($mime->getID() > 0)->toBe(true);
});

test('Load entry', function() 
{
    $this->seed(MimesTableSeeder::class);
    
    $mime = new Mime();
    $mime->load(1);
    
    expect($mime->main)->toBe('audio');
    expect($mime->sub)->toBe('mpeg');
    
});

