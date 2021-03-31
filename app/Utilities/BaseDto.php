<?php
namespace App\Utilities;

abstract class BaseDto
{
    public static function get()
    {
        return new static();
    }
}
