<?php
/**
 * The Query builder Mapper API.
 *
 * @author RN Kushwaha <rn.kushwaha022@gmail.com>
 *
 * @since v0.0.3 <Date: 19th June, 2019>
 */
namespace Dolphin\Mapper;

/**
 * This is base class to extend the Dolphin features
 */
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
        $debug_backtrace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT,2);
        $calledClass = get_called_class();
        $object = new Dolphin();
        $object->tableName = $calledClass;
        
        if(in_array($method,['save'])){
            $obj = new Save();
            $obj->tableName = $calledClass;
            return $obj->$method($debug_backtrace[0]['object']);
        }

        return $object->{$method}(...$parameters);
    }
}
