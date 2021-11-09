<?php

namespace PullWord\Facade;

use think\Facade;
use PullWord\PullWord as Accessor;

class PullWord extends Facade
{
    protected static function getFacadeClass()
    {
        return Accessor::class;
    }
}
