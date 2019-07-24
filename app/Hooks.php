<?php

namespace App;

class Hooks
{
    public static function apply($name, $args)
    {
        if (class_exists("App\Hooks\Hooker") && method_exists("App\Hooks\Hooks", $name)) {
            $hooker = new App\Hooks\Pudge;
            $hooker->$name($args);
        }
    }
}