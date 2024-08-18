<?php

use Tests\TestCase;
use Sunhill\Dedup\Filter\FilterContainer;
use Sunhill\Dedup\FileFilters\NewFile_Ignore;
use Sunhill\Dedup\FileFilters\KnownFile_Ignore;

uses(TestCase::class);

test('test matches', function($filter,$condition, $expect) {
  
    $filter = new $filter();
    $container = new FilterContainer();
    foreach($condition as $key => $value) {
        $container->setCondition($key, $value);
    }
    expect($filter->matches($container))->toBe($expect);
})->with([
    'NewFile_Ignore filter all matches'=>
    [
        NewFile_Ignore::class, 
        [
            'handle_new_file'=>'ignore',
            'is_known_file'=>false,
            'is_new_file'=>true
        ], true        
    ],
    'NewFile_Ignore filter handle mismatches'=>
    [
        NewFile_Ignore::class,
        [
            'handle_new_file'=>'something',
            'is_known_file'=>false,
            'is_new_file'=>true
        ], false
    ],
    'NewFile_Ignore filter newfile mismatches'=>
    [
        NewFile_Ignore::class,
        [
            'handle_new_file'=>'ignore',
            'is_known_file'=>true,
            'is_new_file'=>false
        ], false
    ],
    'KnownFile_Ignore filter all matches'=>
    [
        KnownFile_Ignore::class,
        [
            'handle_known_file'=>'ignore',
            'is_new_file'=>false,
            'is_known_file'=>true
        ], true
    ],
    'KnownFile_Ignore filter handle mismatches'=>
    [
        KnownFile_Ignore::class,
        [
            'handle_known_file'=>'something',
            'is_new_file'=>false,
            'is_known_file'=>true
        ], false
    ],
    'KnownFile_Ignore filter KnownFile mismatches'=>
    [
        KnownFile_Ignore::class,
        [
            'handle_known_file'=>'ignore',
            'is_new_file'=>true,
            'is_known_file'=>false
        ], false
    ],
    
]);

test('test filter result matches', function($filter, $expect, $containerconditions) {
    $container = new FilterContainer();
    $filter = new $filter();
    $filter->setContainer($container);
    expect($filter->execute())->toBe($expect);
    foreach ($containerconditions as $key => $value) {
        expect($container->getCondition($key))->toBe($value);
    }
})->with([
    'NewFile_Ignore'=>[NewFile_Ignore::class,'SUFFICIENTSTOP',['message'=>'Ignoring new file']],
    'KnownFile_Ignore'=>[KnownFile_Ignore::class,'SUFFICIENTSTOP',['message'=>'Ignoring already known file']],
]);
