<?php
/**
 * The Helper class
 *
 * @author RN Kushwaha <rn.kushwaha022@gmail.com>
 *
 * @since v0.0.5 <Date: 9th May, 2019>
 */

namespace Dolphin\Utils;

class Utils
{
    public function decamelize($string) {
        return strtolower(preg_replace(['/([a-z\d])([A-Z])/', '/([^_])([A-Z][a-z])/'], '$1_$2', $string));
    }
    
    /**
     * Turn the stadClass object to the type of calling Model
     *
     * @param String $destination
     * @param Object $sourceObject
     * @return Object $destination
     *
     * @author RN Kushwaha <rn.kushwaha022@gmail.com>
     * @since v0.0.5
     */
    public function turnObject($destination, $sourceObject)
    {
        $destination = new $destination();
        if(!is_object($sourceObject)){
            return $destination;
        }
        
        $sourceReflection = new \ReflectionObject($sourceObject);
        $destinationReflection = new \ReflectionObject($destination);
        $sourceProperties = $sourceReflection->getProperties();
        
        foreach ($sourceProperties as $sourceProperty) {
            $sourceProperty->setAccessible(true);
            $name = $sourceProperty->getName();
            $value = $sourceProperty->getValue($sourceObject);
            
            if ($destinationReflection->hasProperty($name)) {
                $propDest = $destinationReflection->getProperty($name);
                $propDest->setAccessible(true);
                $propDest->setValue($destination,$value);
            } else {
                $destination->$name = $value;
            }
        }
        
        return $destination;
    }
    
     /**
     * Turn the stadClass object to the type of calling Model
     *
     * @param String $destination
     * @param Object $sourceObject
     * @return Object $destination
     *
     * @author RN Kushwaha <rn.kushwaha022@gmail.com>
     * @since v0.0.5
     */
    public function turnObjects($destination, $data)
    {
        $destination = new $destination();
        if(count($data)){
            $destination->data = json_decode(json_encode($data, true));
        }

        return $destination;
    }
}
