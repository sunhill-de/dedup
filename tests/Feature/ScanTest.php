<?php

use Illuminate\Support\Facades\DB;

function setupScenario(int $number)
{
    $temp_dir = dirname(__FILE__).'/../temp';
    $source_dir = dirname(__FILE__).'/Scenarios/Scenario'.$number.'/*';
    exec('rm -rf '.$temp_dir.'/*');
    exec('cp -r '.$source_dir.' '.$temp_dir);
}

test('Simple scan', function() 
{
    setupScenario(1);
    $temp_dir = dirname(__FILE__).'/../temp';
    $this->artisan('migrate:fresh');
    $this->artisan('scan '.$temp_dir);
    
    $this->assertDatabaseHas('hashtable', ['short_hash'=>'606ec6e9bd8a8ff2ad14e5fade3f264471e82251','long_hash'=>'606ec6e9bd8a8ff2ad14e5fade3f264471e82251','size'=>3]);
    $this->assertDatabaseHas('hashtable', ['file_path'=>$temp_dir.'/A.txt']);
    $this->assertDatabaseMissing('hashtable', ['short_hash'=>'4ed61e15c9f84e9fc98ae553ff46010035aac24d','long_hash'=>'4ed61e15c9f84e9fc98ae553ff46010035aac24d','size'=>3]);
});

test('Simple recursive scan', function()
{
    setupScenario(1); 
    $temp_dir = dirname(__FILE__).'/../temp';
    $this->artisan('migrate:fresh');
    $this->artisan('scan --recursive '.$temp_dir);

    $this->assertDatabaseHas('hashtable', ['short_hash'=>'606ec6e9bd8a8ff2ad14e5fade3f264471e82251','long_hash'=>'606ec6e9bd8a8ff2ad14e5fade3f264471e82251','size'=>3]);
    $this->assertDatabaseHas('hashtable', ['short_hash'=>'4ed61e15c9f84e9fc98ae553ff46010035aac24d','long_hash'=>'4ed61e15c9f84e9fc98ae553ff46010035aac24d','size'=>3]);
    $this->assertDatabaseHas('hashtable', ['file_path'=>$temp_dir.'/A.txt']);    
    $this->assertDatabaseHas('hashtable', ['file_path'=>$temp_dir.'/subdir/D.txt']);
    $this->assertDatabaseMissing('hashtable', ['file_path'=>$temp_dir.'/subdir/A.txt']);
});

// ================================== Remove empty dir =========================================
test('Remove primary empty dir (no dry run)', function() 
{
    setupScenario(1);
    $temp_dir = dirname(__FILE__).'/../temp';
    $this->artisan('migrate:fresh');
    
    $this->artisan('scan --recursive --removeemptydirs '.$temp_dir);
    
    expect(file_exists($temp_dir.'/subdir'))->toBe(true);
    expect(file_exists($temp_dir.'/emptydir'))->toBe(false);
});

test('Remove primary empty dir (dry run)', function()
{
    setupScenario(1);
    $temp_dir = dirname(__FILE__).'/../temp';
    $this->artisan('migrate:fresh');
    
    $this->artisan('scan --recursive --removeemptydirs --dryrun '.$temp_dir);
    
    expect(file_exists($temp_dir.'/subdir'))->toBe(true);
    expect(file_exists($temp_dir.'/emptydir'))->toBe(true);
});

// ================================ Handle New ==================================================
test('--newfile=report', function() 
{
    setupScenario(1);
    $temp_dir = dirname(__FILE__).'/../temp';
    $this->artisan('migrate:fresh');

    $this->artisan('scan --newfile=report '.$temp_dir);
    
});

test('--newfile=ignore', function()
{
    setupScenario(1);
    $temp_dir = dirname(__FILE__).'/../temp';
    $this->artisan('migrate:fresh');

    $this->artisan('scan --newfile=ignore '.$temp_dir);
    
});

test('--newfile=move without options' , function()
{
    setupScenario(2);
    $temp_dir = dirname(__FILE__).'/../temp';
    $this->artisan('migrate:fresh');

    $this->artisan('scan --newfile=move --movenew= '.$temp_dir.'/dest '.$temp_dir);
    
});

test('--newfile=move --ignoreprefix=something' , function()
{
    setupScenario(2);
    $temp_dir = dirname(__FILE__).'/../temp';
    $this->artisan('migrate:fresh');

    $this->artisan('scan --newfile=move --ignoreprefix='.$temp_dir.' --movenew= '.$temp_dir.'/dest '.$temp_dir);
    
});

test('--newfile=move --ignoreprefix=something --prefixtype' , function()
{
    setupScenario(2);
    $temp_dir = dirname(__FILE__).'/../temp';
    $this->artisan('migrate:fresh');

    $this->artisan('scan --newfile=move --prefixtype --ignoreprefix='.$temp_dir.' --movenew= '.$temp_dir.'/dest '.$temp_dir);

    expect(file_exists($temp_dir.'/source/A.txt'))->toBe(false);
    expect(file_exists($temp_dir.'/text/A.txt'))->toBe(false);
    expect(file_exists($temp_dir.'/dest/text/A.txt'))->toBe(true);
});

