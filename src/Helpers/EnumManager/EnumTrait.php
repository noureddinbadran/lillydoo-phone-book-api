<?php

namespace App\Helpers\EnumManager;

use ReflectionClass;

trait EnumTrait
{
    public static function getConstants()
    {
        $class = new ReflectionClass(__CLASS__);

        return $class->getConstants();
    }

    public static function getConstant($name)
    {
        $class = new ReflectionClass(__CLASS__);

        return $class->getConstant($name);
    }
}