<?php

use Sunhill\Dedup\File;

test('Long hash works', function()
{
    $test = new File();
    $test->readFile(dirname(__FILE__).'/Samples/testfile.txt');
    expect($test->longHash())->toBe('c5cf529d4635f32dc94969faba60fd333184c267');
});

test('Short hash works', function()
{
    $test = new File();
    $test->readFile(dirname(__FILE__).'/Samples/testfile.txt');
    expect($test->shortHash())->toBe('e94e5d9950014c27804be1df96ca8ad420b90a89');    
});

test('File size works', function()
{
    $test = new File();
    $test->readFile(dirname(__FILE__).'/Samples/testfile.txt');
    expect($test->size())->toBe(80);    
});

test('File mime group works', function($file, $expect)
{
    $test = new File();
    $test->readFile(dirname(__FILE__).'/../SampleFiles/testfiles/'.$file);
    expect($test->getMimeGroup())->toBe($expect);    
})->with([
    ['audio-flac/test.flac','audio'],   
    ['audio-mp3/test.mp3','audio'],
    ['image-heif/test.heic','image'],
    ['image-jpeg/test.jpg','image'],
    ['application-testfile/A.qqq','text']
]);

test('File mime subgroup works', function($file, $expect)
{
    $test = new File();
    $test->readFile(dirname(__FILE__).'/../SampleFiles/testfiles/'.$file);
    expect($test->getMimeSubGroup())->toBe($expect);
})->with([
    ['audio-flac/test.flac','flac'],
    ['audio-mp3/test.mp3','mpeg'],
    ['image-heif/test.heic','heif'],
    ['image-jpeg/test.jpg','jpeg'],
    ['application-testfile/A.qqq','plain']
]);

test('setCondition() and getCondition() works with scalar', function()
{
    $test = new File();
    $test->setCondition('something',5);
    expect($test->getCondition('something'))->toBe(5);
});

test('setCondition() and getCondition() works with default', function()
{
    $test = new File();
    $test->setCondition('something');
    expect($test->getCondition('something'))->toBe(true);    
});

test('set_something() and get_something() works with callback', function()
{
    $test = new File();
    $test->setCondition('something',function($file) 
    {
        return 5;
    });
    expect($test->getCondition('something'))->toBe(5);    
});

