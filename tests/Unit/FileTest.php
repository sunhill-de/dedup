<?php

use Sunhill\Dedup\File;

test('Long hash works', function()
{
    $test = new File(dirname(__FILE__).'/Samples/testfile.txt');
    expect($test->longHash())->toBe('c5cf529d4635f32dc94969faba60fd333184c267');
});

test('Short hash works', function()
{
    $test = new File(dirname(__FILE__).'/Samples/testfile.txt');
    expect($test->shortHash())->toBe('e94e5d9950014c27804be1df96ca8ad420b90a89');    
});

test('File size works', function()
{
    $test = new File(dirname(__FILE__).'/Samples/testfile.txt');
    expect($test->size())->toBe(80);    
});