<?php

use Sunhill\Dedup\Tests\TestCase;

test('Scanning works with defaults', function()
{
   $this->artisan('scan '.dirname(__FILE__).'/testfiles')->asserExitCode(0); 
});