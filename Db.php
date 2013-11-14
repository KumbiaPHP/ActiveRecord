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

namespace ActiveRecord;

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
	private static $_pool = array();
	
	/**
	 * Obtiene manejador de conexion a la base de datos
	 *
	 * @param string $database base de datos a conectar
	 * @param boolean $force forzar nueva conexion PDO
	 * @return PDO
	 * @throw KumbiaException
	 */
	public static function get($database = 'default', $force = false)
	{
		// Verifica el singleton
		if(!$force && isset(self::$_pool[$database])) return self::$_pool[$database];
		
		// Leer la configuración de conexión
		$config = require_once(APP_PATH.'config/databases.php');
		
		if(!isset($config[$database])) throw new \KumbiaException("No existen datos de conexión para '$database' en config/databases.php");
		
		$config = $config[$database];
		
		// carga los valores por defecto para la conexión, si no existen
		$config = $config + array('username' => NULL, 'password' => NULL, 'params' => array());
		
		try {
			$dbh = new PDO($config['dsn'], $config['username'], $config['password'], $config['params']);
		} catch (PDOException $e) { //TODO: comprobar
			if (!extension_loaded('pdo')) throw new \KumbiaException('Debe cargar la extensión de PHP llamada php_pdo');
			throw new \KumbiaException("No se pudo realizar la conexión con $database, compruebe su configuración.");
		}
		
		if(!$force) self::$_pool[$database] = $dbh;
		
		return $dbh;
	}
}
