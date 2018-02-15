<?php
/**
 * KumbiaPHP web & app Framework.
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
 *
 * @copyright  2005 - 2016  Kumbia Team (http://www.kumbiaphp.com)
 * @license    http://wiki.kumbiaphp.com/Licencia     New BSD License
 */
namespace Kumbia\ActiveRecord;

/**
 * Autoload de las clases.
 */
class Autoloader
{
    private static $folder;

    /**
     * Registra el autoloader.
     *
     * @param bool $prepend
     */
    public static function register($prepend = false)
    {
        spl_autoload_register([__CLASS__, 'autoload'], true, $prepend);
        self::$folder = dirname(dirname(__DIR__)).'/';
    }

    /**
     * @param string $className
     */
    public static function autoload($className)
    {
        if (0 !== strpos($className, 'Kumbia\\ActiveRecord')) {
            return;
        }

        $fileName = str_replace(['_', '\\'], DIRECTORY_SEPARATOR, $className).'.php';
        require self::$folder.$fileName;
    }
}
