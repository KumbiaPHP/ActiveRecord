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

use PDO;

/**
 * Manejador de conexiones de base de datos.
 */
abstract class Db
{
    /**
     * Pool de conexiones.
     *
     * @var array
     */
    private static $pool = [];

    /**
     * Config de conexiones.
     *
     * @var array
     */
    private static $config = [];

    /**
     * Obtiene manejador de conexión a la base de datos.
     *
     * @param string $database base de datos a conectar
     * @param bool   $force    forzar nueva conexion PDO
     *
     * @return PDO
     * @throw KumbiaException
     */
    public static function get($database = 'default', $force = false)
    {
        // Verifica el singleton
        if (!$force && isset(self::$pool[$database])) {
            return self::$pool[$database];
        }

        if ($force) {
            return self::connect(self::getConfig($database));
        }

        return self::$pool[$database] = self::connect(self::getConfig($database));
    }

    /**
     * Conexión a la base de datos.
     *
     * @param array $config Config base de datos a conectar
     *
     * @return PDO
     */
    private static function connect($config)
    {
        try {
            return new PDO($config['dsn'], $config['username'], $config['password'], $config['params']);
        } catch (\PDOException $e) { //TODO: comprobar
                $message = $e->getMessage();
            throw new \RuntimeException("No se pudo realizar la conexión con '{$config['dsn']}'. {$message}");
        }
    }

    /**
     * Obtiene manejador de conexión a la base de datos.
     *
     * @param string $database base de datos a conectar
     *
     * @return array
     */
    private static function getConfig($database)
    {
        if (empty(self::$config)) {
            // Leer la configuración de conexión
            self::$config = require APP_PATH.'config/databases.php';
        }
        if (!isset(self::$config[$database])) {
            throw new \RuntimeException("No existen datos de conexión para la bd '$database' en ".APP_PATH.'config/databases.php');
        }

            // Envia y carga los valores por defecto para la conexión, si no existen
        return self::$config[$database] +  [
                'dns'      => null,
                'username' => null,
                'password' => null,
                'params'   => [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION],
            ];
    }

    /**
     * Permite agregar una base de datos sin leer del archivo de configuracion.
     *
     * @param array $value Valores de la configuración
     */
    public static function setConfig(array $value)
    {
        self::$config = [] +  self::$config + $value;
    }
}
