<?php

use Tests\TestCase;
use Sunhill\Dedup\Filter\FilterContainer;
use Sunhill\Dedup\FileFilters\NewFile_Ignore;

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
    
]);

test('test filter result matches', function($filter, $expect) {
    $filter = new $filter();
    expect($filter->execute())->toBe($expect);    
})->with([
    [NewFile_Ignore::class,'SUFFICIENTSTOP'],
]);
