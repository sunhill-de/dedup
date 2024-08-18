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
   
   $mime = Mime::searchValues('Audio/Mpeg');
   
   expect($mime->main)->toBe('Audio');
});

test('Find entry with success (single mime)', function()
{
    $this->seed(MimesTableSeeder::class);
    
    $mime = Mime::searchValues('Audio','Mpeg');
    
    expect($mime->main)->toBe('Audio');
});

test('Find entry with failure', function()
{
    $this->seed(MimesTableSeeder::class);
    
    $mime = Mime::searchValues('Image/Jpeg');
    
    expect($mime)->toBe(null);
});

test('Add entry', function() 
{
    $this->seed(MimesTableSeeder::class);
    
    $mime = new Mime();
    $mime->main = 'Image';
    $mime->sub = 'Jpeg';
    
    $mime->commit();
    
    $this->assertDatabaseHas('mimes',['main'=>'Image','sub'=>'Jpeg']);
});

