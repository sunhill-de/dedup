<?php

use Sunhill\Dedup\FileUtilException;
use Illuminate\Support\Str;

/**
 * Checks if the given dir is empty
 * 
 * @param string $dir
 * @return bool
 */
function isDirEmpty(string $dir): bool
{
    if (!file_exists($dir) || !is_dir($dir)) {
        throw new FileUtilException("'$dir' is not a dir or does not exist.");
    }
    $dir_obj = dir($dir);
    while (($entry = $dir_obj->read()) !== false) {
        if (($entry == '.') || ($entry == '..')) {
            continue;
        }
        $dir_obj->close();
        return false;
    }
    $dir_obj->close();
    return true;
}

/**
 * Checks if the given link target exists (or if the link is pointing to a non existant 
 * target
 * 
 * @param string $link
 * @return bool
 */
function doesLinkTargetExist(string $link): bool
{
    if (!is_link($link)) {
        throw new FileUtilException("'$link' is not a link or does not exist");
    }
    $target = readlink($link);
    if (!Str::startsWith($target, DIRECTORY_SEPARATOR)) {
        $target = pathinfo($link,PATHINFO_DIRNAME).DIRECTORY_SEPARATOR.$target;
    }
    return file_exists($target); 
}

/** Creates the given dir and all of its parent dirs 
 * 
 * @param string $target
 */
function createDirRecursive(string $target)
{
    if (file_exists($target)) {
        if (is_dir($target)) {
            return;
        }
        throw new FileUtilException("'$target' already exists as a file");
    }
    $parts = explode(DIRECTORY_SEPARATOR, $target);
    array_pop($parts);
    if (!empty($parts)) {
        createDirRecursive(implode(DIRECTORY_SEPARATOR, $parts));
    }
    mkdir($target);
}

/**
 * Removes thes given directory recursive
 * 
 * @param string $target
 */
function removeDirRecursive(string $target)
{
    if (!is_dir($target)) {
        throw new FileUtilException("'$target' is not a directory.");
    }
    $dir = dir($target);
    while (($entry = $dir->read()) !== false) {
        if (($entry == '.') || ($entry == '..')) {
            continue;
        }
        if (is_dir($target.DIRECTORY_SEPARATOR.$entry)) {
            removeDirRecursive($target.DIRECTORY_SEPARATOR.$entry);
        } else {
            unlink($target.DIRECTORY_SEPARATOR.$entry);
        }        
    }
    rmdir($target);
    $dir->close();
}

/**
 * Checks if the given filenbame is alread uses. If yes, adds a serail number till
 * the filename is unique
 * 
 * @param string $target
 * @return string
 */
function getUniqueFilename(string $target): string
{
    if (!file_exists($target)) {
        return $target;
    }
    $infos = pathinfo($target,PATHINFO_ALL);
    $counter = 0;
    do {
       $target = $infos['dirname'].DIRECTORY_SEPARATOR.$infos['filename'].'-'.str_repeat('0',3-strlen($counter++)).$counter.'.'.$infos['extension'];  
    } while (file_exists($target));
    return $target;
}

/**
 * Creates a link from $link_path to $target_path. If $make_unique is set, the 
 * $target_path is made unique. If the link_target does not exists, ot raises an 
 * exception. If the link_path already exists (with no $make_unique set) if returns false
 * otherwise it returns if the link was created successfully.
 * 
 * @param string $link_path
 * @param string $target_path
 * @param bool $make_unique
 * @return bool
 */
function linkFile(string $link_path, string $target_path, bool $make_unique = true): bool
{
    if ($make_unique) {
        $link_path = getUniqueFilename($link_path);
    }
    if (file_exists($link_path)) {
        return false;
    }
    if (!file_exists($target_path)) {
        throw new FileUtilException("The link target '$target_path' does not exist.");
    }
    if (!file_exists(pathinfo($link_path,PATHINFO_DIRNAME))) {
        createDirRecursive(pathinfo($link_path, PATHINFO_DIRNAME));
    }
    symlink($target_path, $link_path);
    return file_exists($link_path);
}

function moveFile(string $source, string $target, bool $make_unique = true): bool
{
    if ($make_unique) {
        $target = getUniqueFilename($target);
    }
    if (file_exists($target)) {
        return false;
    }
    if (!file_exists($source)) {
        throw new FileUtilException("The source '$source' does not exist.");
    }
    if (!file_exists(pathinfo($target,PATHINFO_DIRNAME))) {
        createDirRecursive(pathinfo($target, PATHINFO_DIRNAME));
    }
    rename($source, $target); 
    return file_exists($target);
}

function copyFile(string $source, string $target, bool $make_unique = true): bool
{
    if ($make_unique) {
        $target = getUniqueFilename($target);
    }
    if (file_exists($target)) {
        return false;
    }
    if (!file_exists($source)) {
        throw new FileUtilException("The source '$source' does not exist.");
    }
    if (!file_exists(pathinfo($target,PATHINFO_DIRNAME))) {
        createDirRecursive(pathinfo($target, PATHINFO_DIRNAME));
    }
    copy($source, $target);
    return file_exists($target);
}

/**
 * Removes the given file
 * 
 * @param string $target
 * @return bool
 */
function removeFile(string $target): bool
{
    if (!file_exists($target)) {
        return true; // already removed
    }
    if (is_dir($target)) {
        throw new FileUtilException("'$target' is a directory. Use removeDir()");
    }
    unlink($target);
    
    return !file_exists($target);
}
