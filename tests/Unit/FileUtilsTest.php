<?php

use Sunhill\Dedup\FileUtilException;

require_once(dirname(__FILE__).'/../../Lib/FileUtils.php');

test('isDirEmpty() with non empty dir', function()
{
    expect(isDirEmpty(dirname(__FILE__).'/Samples/nonemptydir'))->toBe(false);    
})->group('dir');

test('isDirEmpty() with empty dir', function()
{
    expect(isDirEmpty(dirname(__FILE__).'/Samples/emptydir'))->toBe(true);    
})->group('dir');;

test('doesLinkTargetExist() success', function()
{
    expect(doesLinkTargetExist(dirname(__FILE__).'/Samples/link_to_existing.txt'))->toBe(true); 
})->group('link');;

test('doesLinkTargetExist() failure', function()
{
    expect(doesLinkTargetExist(dirname(__FILE__).'/Samples/link_to_nonexisting.txt'))->toBe(false);    
})->group('link');;

test('createDirRecursive()', function()
{
    if (file_exists(dirname(__FILE__).'/Samples/testdir')) {
        exec('rm -r '.dirname(__FILE__).'/Samples/testdir');
    };
    createDirRecursive(dirname(__FILE__).'/Samples/testdir/subdir/subdir2');
    expect(file_exists(dirname(__FILE__).'/Samples/testdir/subdir/subdir2'))->toBe(true);
})->group('dir');;

test('removeDirRecursive()', function()
{
    if (!file_exists(file_exists(dirname(__FILE__).'/Samples/testdir/subdir/subdir2'))) {
        $command = 'mkdir -p '.dirname(__FILE__).'/Samples/testdir/subdir/subdir2';
        exec($command);
    }
    removeDirRecursive(dirname(__FILE__).'/Samples/testdir');
    expect(file_exists(dirname(__FILE__).'/Samples/testdir'))->toBe(false);
})->group('dir');;

test('getUniqueFilename', function($destination, $expect)
{
    if (file_exists(dirname(__FILE__).'/Samples/manyfile-003.txt')) {
        exec('rm '.dirname(__FILE__).'/Samples/manyfile-003.txt');
    }
    expect(getUniqueFilename(dirname(__FILE__).'/Samples/'.$destination))->toBe(dirname(__FILE__).'/Samples/'.$expect); 
})->with([
    ['Newfile.txt', 'Newfile.txt'],
    ['ShortFile.txt','ShortFile-001.txt'],
    ['testfile.txt', 'testfile-002.txt'],
    ['manyfile.txt', 'manyfile-003.txt']
])->group('unique');

test('linkFile() success', function()
{
    if (file_exists(dirname(__FILE__).'/Samples/Link.txt')) {
        exec('rm '.dirname(__FILE__).'/Samples/Link.txt');
    }
    linkFile(dirname(__FILE__).'/Samples/Link.txt', dirname(__FILE__).'/Samples/testfile.txt');
    
    expect(file_exists(dirname(__FILE__).'/Samples/Link.txt'))->toBe(true);
    expect(readlink(dirname(__FILE__).'/Samples/Link.txt'))->toBe(dirname(__FILE__).'/Samples/testfile.txt');
    
    exec('rm '.dirname(__FILE__).'/Samples/Link.txt');
})->group('link');

test('linkFile() success with makeunique = true', function()
{
    if (file_exists(dirname(__FILE__).'/Samples/manyfile-003.txt')) {
        exec('rm '.dirname(__FILE__).'/Samples/manyfile-003.txt');
    }
    linkFile(dirname(__FILE__).'/Samples/manyfile.txt', dirname(__FILE__).'/Samples/testfile.txt');

    expect(file_exists(dirname(__FILE__).'/Samples/manyfile-003.txt'))->toBe(true);
    expect(readlink(dirname(__FILE__).'/Samples/manyfile-003.txt'))->toBe(dirname(__FILE__).'/Samples/testfile.txt');
    
    exec('rm '.dirname(__FILE__).'/Samples/manyfile-003.txt');
})->group('link');

test('linkFile() success with makeunique = false', function()
{
    expect(linkFile(dirname(__FILE__).'/Samples/manyfile.txt', dirname(__FILE__).'/Samples/testfile.txt',false))->toBe(false);
})->group('link');

test('linkFile() failure', function()
{
    if (file_exists(dirname(__FILE__).'/Samples/Link.txt')) {
        exec('rm '.dirname(__FILE__).'/Samples/Link.txt');
    }
    linkFile(dirname(__FILE__).'/Samples/Link.txt', dirname(__FILE__).'/Samples/nonexisting.txt');
})->group('link')->throws(FileUtilException::class);

test('linkfile() and derstination dir does not exist', function()
{
    if (file_exists(dirname(__FILE__).'/Samples/destination')) {
        exec('rm -rf '.dirname(__FILE__).'/Samples/destination');
    }
    linkFile(dirname(__FILE__).'/Samples/destination/Link.txt', dirname(__FILE__).'/Samples/testfile.txt');   

    expect(file_exists(dirname(__FILE__).'/Samples/destination/Link.txt'))->toBe(true);
})->group('link');

test('copyFile()', function()
{
    if (file_exists(dirname(__FILE__).'/Samples/nonemptydir/destfile.txt')) {
        exec('rm -rf '.dirname(__FILE__).'/Samples/nonemptydir/destfile.txt');
    }
    copyFile(dirname(__FILE__).'/Samples/testfile.txt',dirname(__FILE__).'/Samples/nonemptydir/destfile.txt');
    
    expect(file_exists(dirname(__FILE__).'/Samples/nonemptydir/destfile.txt'))->toBe(true);
    expect(file_exists(dirname(__FILE__).'/Samples/testfile.txt'))->toBe(true);
    
    exec('rm '.dirname(__FILE__).'/Samples/nonemptydir/destfile.txt');
})->group('copy');

test('copyFile() with makeunique = true', function()
{
    if (file_exists(dirname(__FILE__).'/Samples/manyfile-003.txt')) {
        exec('rm -rf '.dirname(__FILE__).'/Samples/manyfile-003.txt');
    }
    copyFile(dirname(__FILE__).'/Samples/testfile.txt',dirname(__FILE__).'/Samples/manyfile.txt');
    
    expect(file_exists(dirname(__FILE__).'/Samples/manyfile-003.txt'))->toBe(true);
    expect(file_exists(dirname(__FILE__).'/Samples/testfile.txt'))->toBe(true);
    
    exec('rm '.dirname(__FILE__).'/Samples/manyfile-003.txt');
})->group('copy');

test('copyFile() with makeunique = false', function()
{
    expect(copyFile(dirname(__FILE__).'/Samples/testfile.txt',dirname(__FILE__).'/Samples/manyfile.txt',false))->toBe(false);    
})->group('copy');

test('copyFile() with destination dir does not exists', function()
{
    if (file_exists(dirname(__FILE__).'/Samples/destination')) {
        exec('rm -rf '.dirname(__FILE__).'/Samples/destination');
    }
    copyFile(dirname(__FILE__).'/Samples/testfile.txt',dirname(__FILE__).'/Samples/destination/testfile.txt');
    
    expect(file_exists(dirname(__FILE__).'/Samples/destination/testfile.txt'))->toBe(true);
    expect(file_exists(dirname(__FILE__).'/Samples/testfile.txt'))->toBe(true);
    
    exec('rm -rf '.dirname(__FILE__).'/Samples/destination');
})->group('copy');


test('moveFile()', function()
{
    if (file_exists(dirname(__FILE__).'/Samples/nonemptydir/destfile.txt')) {
        exec('rm -rf '.dirname(__FILE__).'/Samples/nonemptydir/destfile.txt');
    }
    if (!file_exists(dirname(__FILE__).'/Samples/movefile.txt')) {
        exec('cp '.dirname(__FILE__).'/Samples/testfile.txt'.' '.dirname(__FILE__).'/Samples/movefile.txt');
    }
    moveFile(dirname(__FILE__).'/Samples/movefile.txt',dirname(__FILE__).'/Samples/nonemptydir/destfile.txt');
    
    expect(file_exists(dirname(__FILE__).'/Samples/nonemptydir/destfile.txt'))->toBe(true);
    expect(file_exists(dirname(__FILE__).'/Samples/movefile.txt'))->toBe(false);

    exec('rm '.dirname(__FILE__).'/Sample/movefile.txt');    
})->group('move');

test('moveFile() with makeunique = true', function()
{
    if (file_exists(dirname(__FILE__).'/Samples/manyfile-003.txt')) {
        exec('rm -rf '.dirname(__FILE__).'/Samples/manyfile-003.txt');
    }
    if (!file_exists(dirname(__FILE__).'/Samples/movefile.txt')) {
        exec('cp '.dirname(__FILE__).'/Samples/testfile.txt'.' '.dirname(__FILE__).'/Samples/movefile.txt');
    }
    moveFile(dirname(__FILE__).'/Samples/movefile.txt',dirname(__FILE__).'/Samples/manyfile.txt');
    
    expect(file_exists(dirname(__FILE__).'/Samples/manyfile-003.txt'))->toBe(true);
    expect(file_exists(dirname(__FILE__).'/Samples/movefile.txt'))->toBe(false);
    
    exec('rm '.dirname(__FILE__).'/Sample/manyfile-003.txt');
    exec('rm '.dirname(__FILE__).'/Sample/movefile.txt');
})->group('move');

test('moveFile() with makeunique = false', function()
{
    if (!file_exists(dirname(__FILE__).'/Samples/movefile.txt')) {
        exec('cp '.dirname(__FILE__).'/Samples/testfile.txt'.' '.dirname(__FILE__).'/Samples/movefile.txt');
    }
    $result = moveFile(dirname(__FILE__).'/Samples/movefile.txt',dirname(__FILE__).'/Samples/manyfile.txt', false);
    
    expect($result)->toBe(false);
    
    exec('rm '.dirname(__FILE__).'/Sample/movefile.txt');
})->group('move');

test('moveFile() with destination dir does not exists', function()
{
    if (!file_exists(dirname(__FILE__).'/Samples/movefile.txt')) {
        exec('cp '.dirname(__FILE__).'/Samples/testfile.txt'.' '.dirname(__FILE__).'/Samples/movefile.txt');
    }
    if (file_exists(dirname(__FILE__).'/Samples/destination')) {
        exec('rm -rf '.dirname(__FILE__).'/Samples/destination');
    }
    moveFile(dirname(__FILE__).'/Samples/movefile.txt',dirname(__FILE__).'/Samples/destination/testfile.txt');
    
    expect(file_exists(dirname(__FILE__).'/Samples/destination/testfile.txt'))->toBe(true);
    expect(file_exists(dirname(__FILE__).'/Samples/movefile.txt'))->toBe(false);
    
    exec('rm -rf '.dirname(__FILE__).'/Samples/destination');
})->group('move');


test('removeFile()', function()
{
    if (!file_exists(dirname(__FILE__).'/Samples/movefile.txt')) {
        exec('cp '.dirname(__FILE__).'/Samples/testfile.txt'.' '.dirname(__FILE__).'/Samples/movefile.txt');
    }
    expect(file_exists(dirname(__FILE__).'/Samples/movefile.txt'))->toBe(true);
    
    expect(removeFile(dirname(__FILE__).'/Samples/movefile.txt'))->toBe(true);
    
    expect(file_exists(dirname(__FILE__).'/Samples/movefile.txt'))->toBe(false);
})->group('delete');

