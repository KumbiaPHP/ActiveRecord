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
	public static function get($database = null, $force = false) 
	{		
		// Verifica el singleton
		if(!$force && isset(self::$_pool[$database])) return self::$_pool[$database];
		
		// Leer la especificación de conexión
		$databases = \Config::read('databases');
		
		if(!isset($databases[$database])) throw new KumbiaException("No existe la especificación '$database' para conexión a base de datos en databases.ini");
		
        $config = $databases[$database];

        // carga los valores por defecto para la conexión, si no existen
        $default = array('port' => 0, 'dsn' => NULL, 'dbname' => NULL, 'host' => 'localhost', 'username' => NULL, 'password' => NULL);
        $config = $config + $default;

		if (!extension_loaded('pdo')) throw new KumbiaException('Debe cargar la extensión de PHP llamada php_pdo');

        try {
            $dbh = new \PDO($config['type'] . ":" . $config['dsn'], $config['username'], $config['password']);
            if (!$dbh) throw new KumbiaException("No se pudo realizar la conexion con {$config['type']}");
            
            if ($config['type'] != 'odbc') {
                $dbh->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                $dbh->setAttribute(\PDO::ATTR_CASE, \PDO::CASE_LOWER);
                $dbh->setAttribute(\PDO::ATTR_CURSOR, \PDO::CURSOR_FWDONLY);
            }
            
            //Selecciona charset
            if ($config['type'] == 'mysql' && isset($config['charset'])) {
                $dbh->exec('set character set ' . $config['charset']);
            }
            
            // Si no se forzó una nueva conexión, entonces se almacena en el pool de conexiones
            if(!$force) self::$_pool[$database] = $dbh;
            
            return $dbh;
            
        } catch (\PDOException $e) {
            throw new \KumbiaException($e->getMessage());
        }
	}
}
