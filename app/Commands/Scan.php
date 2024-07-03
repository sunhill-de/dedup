<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;
use Sunhill\Dedup\Scanner;

class Scan extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scan {--r|recursive} {--d|dryrun} {--newfile=report} {--knownfile=report} {--nocache} {--movenew=} {--moveknown=} {--ignoreprefix=} {--p|prefixtype} {--f|followlinks} {--e|removeemptydirs} {directory=.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'scan 
                                {--r|recursive : Directories should be scanned recursivly} 
                                {--d|dryrun : Just say what you would do} 
                                {--newfile=report : How to handle new files. Options are report, ignore, move} 
                                {--knownfile=report : How to handle known files. Options are ignore, delete, link, report, move} 
                                {--nocache : Dont fill the file cache} 
                                {--movenew= : When --newfile=move this parameter indicates where to move the new file} 
                                {--moveknown= : When --knownfile=move this parameter indicates where to move the known file} 
                                {--ignoreprefix= : When any file action is move this prefix is removed before moving}
                                {--p|prefixtype : When set in combination with move the mime type group (audio. video, document) is prefixed before moving } 
                                {--f|followlinks : When set, links are followed} 
                                {--e|removeemptydirs : When a directory is scanned and detected empty remove it} 
                                {directory : The directory to scan}';

    protected function parseDirectory(Scanner $scanner)
    {
        $scanner->setDirectory($this->argument('directory'));        
    }
    
    protected function parseRecursive(Scanner $scanner)
    {
        if ($this->option('recursive')) {
            $scanner->setRecursive();
        }        
    }

    protected function parseNewFile(Scanner $scanner)
    {
        switch ($this->option('newfile')) {
            case 'move':
                if (empty($this->option('movenew'))) {
                    $this->error('--newfile is called with move but no destination is given.');
                    return 1;
                }
                $scanner->setNewFileDesination($this->option('movenew'));
            case 'report':
            case 'ignore':
                $scanner->setNewFileAction($this->option('newfile'));
                break;
            default:
                $this->error('Unkown action for --newfile: '.$this->option('newfile'));
                return 1;
        }        
    }
    
    protected function parseKnownFile(Scanner $scanner)
    {
        switch ($this->option('knownfile')) {
            case 'move':
                if (empty($this->option('moveknown'))) {
                    $this->error('--knownfile is called with move but no destination is given.');
                    return 1;
                }
                $scanner->setknownFileDesination($this->option('moveknown'));
            case 'link':
            case 'delete':
            case 'report':
            case 'ignore':
                $scanner->setknownFileAction($this->option('knownfile'));
                break;
            default:
                $this->error('Unkown action for --knownfile: '.$this->option('knownfile'));
                return 1;
        }        
    }

    protected function parseNoCache(Scanner $scanner)
    {
        if ($this->option('nocache')) {
            $scanner->setNoCache();
        }        
    }
    
    protected function parseDryRun(Scanner $scanner)
    {
        if ($this->option('dryrun')) {
            $scanner->setDryRun();
        }
    }
    
    protected function parseRemoveEmptyDirs(Scanner $scanner)
    {
        if ($this->option('removeemptydirs')) {
            $scanner->setRemoveEmptyDirs();
        }
    }
    
    protected function parseFollowLinks(Scanner $scanner)
    {
        if ($this->option('followlinks')) {
            $scanner->setFollowLinks();
        }
    }
    
    protected function checkForMove()
    {
        if (($this->option('newfile') == 'move') || ($this->option('knownfile') == 'move')) {
            return true;
        }
        $this->error('--prefixtype or --ignoreprefix only makes sense with any move action');
        return false;
    }
    
    protected function parsePrefixType(Scanner $scanner)
    {
        if ($this->option('prefixtype')) {
            $scanner->setPrefixType();
            return $this->checkForMove();
        }
        return true;
    }
    
    protected function parseIgnorePrefix(Scanner $scanner)
    {
        if (!empty($this->option('ignoreprefix'))) {
            $scanner->setIgnorePrefix($this->option('ignnoreprefix'));
            return $this->checkForMove();
        }
        return true;
    }
    
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $scanner = new Scanner();
        $this->parseDirectory($scanner);
        $this->parseRecursive($scanner);
        $this->parseDryRun($scanner);
        $this->parseNewfile($scanner);
        $this->parseKnownFile($scanner);
        $this->parseNoCache($scanner);
        $this->parseFollowLinks($scanner);
        $this->parseRemoveEmptyDirs($scanner);
        if (!$this->parseIgnorePrefix($scanner) && !$this->parsePrefixType($scanner)) {
            return 1;
        }
        try {
            $scanner->run();
        } catch (\Exception $e) {
            $this->error($e->getMessage());
            return 1;
        }
        return 0;
    }

    /**
     * Define the command's schedule.
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
