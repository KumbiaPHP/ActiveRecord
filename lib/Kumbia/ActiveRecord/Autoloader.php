<?php

/**
 * KumbiaPHP web & app Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://wiki.kumbiaphp.com/Licencia
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@kumbiaphp.com so we can send you a copy immediately.
 *
 * @category   Kumbia
 * @package    ActiveRecord
 * @copyright  Copyright (c) 2005-2013 Kumbia Team (http://www.kumbiaphp.com)
 * @license    http://wiki.kumbiaphp.com/Licencia     New BSD License
 */

namespace Kumbia\ActiveRecord;

/**
 * Autoload de las clases
 *
 */
class Autoloader
{

    private static $folder;

    /**
     * Registra el autoloader
     *
     * @param boolean $prepend
     */
    public static function register($prepend = false)
    {
        spl_autoload_register(array(__CLASS__, 'autoload'), true, $prepend);
        self::$folder = dirname(dirname(__DIR__)) . '/';
    }

    /**
     * @param string $class
     */
    public static function autoload($className)
    {
        $className = ltrim($className, '\\');

        if (0 !== strpos($className, 'Kumbia\\ActiveRecord')) {
            return;
        }

        $className = ltrim($className, '\\');
        $fileName = '';
        $namespace = '';
        if ($lastNsPos = strrpos($className, '\\')) {
            $namespace = substr($className, 0, $lastNsPos);
            $className = substr($className, $lastNsPos + 1);
            $fileName = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
        }
        $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';

        $file = self::$folder . DIRECTORY_SEPARATOR . $fileName;
        if (file_exists($file)) {
            require $file;
        }
    }

}
/**
 * @TODO remover esto luego
 * No funciona la autocarga ya que no es una clase
 */
require(__DIR__.'/Query/query_exec.php');