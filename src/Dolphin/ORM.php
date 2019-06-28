<?php

namespace Dolphin\Mapper;

class ORM
{
    public static $class = null;

    public static function getClass()
    {
        if (null == self::$class) {
            self::$class = new Dolphin();
        }

        return self::$class;
    }

    public static function __callStatic($method, $parameters)
    {
        $calledClass = get_called_class();
        $object = self::getClass();
        $object->tableName = $calledClass;

        return $object->{$method}(...$parameters);
    }

    public function __call($method, $parameters)
    {
        if($method== 'asArray'){
            // $obj = new Utils();
        }
    }
}
