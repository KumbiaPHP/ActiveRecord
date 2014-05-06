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
 * @copyright  Copyright (c) 2005-2014  Kumbia Team (http://www.kumbiaphp.com)
 * @license    http://wiki.kumbiaphp.com/Licencia     New BSD License
 */

namespace Kumbia\ActiveRecord;

use PDO;

/**
 * Manejador de conexiones de base de datos
 *
 */
abstract class Db
{
    /**
     * Pool de conexiones
     *
     * @var array
     */
    private static $pool = array();
	
	/**
     * Config de conexiones
     *
     * @var array
     */
    private static $config = array();

    /**
     * Obtiene manejador de conexión a la base de datos
     *
     * @param  string  $database base de datos a conectar
     * @param  boolean $force    forzar nueva conexion PDO
     * @return PDO
     * @throw KumbiaException
     */
    public static function get($database = 'default', $force = false)
    {
        // Verifica el singleton
        if(!$force && isset(self::$pool[$database]))
			return self::$pool[$database];
		
		if($force) return self::connect(self::getConfig($database));
		
		return self::$pool[$database] = self::connect(self::getConfig($database));
	}
	
	/**
     * Conexión a la base de datos
     *
     * @param  array  $config Config base de datos a conectar
     * @return PDO
     */
	private static function connect($config)
	{
        try {
            $dbh = new PDO($config['dsn'], $config['username'], $config['password'], $config['params']);
        } catch (\PDOException $e) { //TODO: comprobar
            throw new \KumbiaException("No se pudo realizar la conexión con $database, compruebe su configuración.");
        }

        return $dbh;
    }
	
	/**
     * Obtiene manejador de conexión a la base de datos
     *
     * @param  string  $database base de datos a conectar
     * @return array
     */
	private static function getConfig($database)
	{
		if(!self::$config) {
			// Leer la configuración de conexión
			self::$config = require(APP_PATH.'config/databases.php');
		}
		if(!isset(self::$config[$database])) throw new \KumbiaException("No existen datos de conexión para la bd '$database' en ".APP_PATH."config/databases.php");
			
        // Envia y carga los valores por defecto para la conexión, si no existen
		return self::$config[$database] + array(
            'dns'      => NULL,
            'username' => NULL,
            'password' => NULL,
            'params' => array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
        );
	}

    /**
     * Permite agregar una base de datos sin leer del archivo de configuracion
     * @param string $database Nombre de la conexion
     * @param Array  $value Valores de la configuración
     */
    static function setConfig( Array $value, $database='default'){
        self::$config = array()+  self::$config+ $value;
    }
}
