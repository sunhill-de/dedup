<?php

use Sunhill\Dedup\Scanner;

test('buildDestination() works', function($file, $dest, $ignore_prefix, $prefix_type, $expect) 
{
    $test = new Scanner();
    expect($test->buildDestination($file, $dest, $ignore_prefix, $prefix_type))->toBe($expect);
})->with([
    ['/this/is/a/test','/that/is/another/destination/','','','/that/is/another/destination/this/is/a/test'],
    ['this/is/a/test','/that/is/another/destination/','','','/that/is/another/destination/this/is/a/test'],
    ['/this/is/a/test','/that/is/another/destination/','/this/is/','','/that/is/another/destination/a/test'],
    ['/this/is/a/test','/that/is/another/destination/','/this/is/','image','/that/is/another/destination/image/a/test'],
]);