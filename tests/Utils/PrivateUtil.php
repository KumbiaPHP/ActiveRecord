<?php


class PrivateUtil
{
    /**
     * To test protected and private methods
     *
     * @param object            $obj
     * @param string            $methodName
     * @param array             $args
     * 
     * @return mixed
     */
    public static function callMethod($obj, $methodName, array $args)
    {
        $method = new \ReflectionMethod($obj, $methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($obj, $args);
    }
}
