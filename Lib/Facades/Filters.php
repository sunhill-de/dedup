<?php
namespace Sunhill\Dedup\Facades;

use Illuminate\Support\Facades\Facade;

class Filters extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'filters';
    }
}
