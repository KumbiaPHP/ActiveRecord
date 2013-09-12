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
 * @subpackage mysql
 * @copyright  Copyright (c) 2005-2013 Kumbia Team (http://www.kumbiaphp.com)
 * @license    http://wiki.kumbiaphp.com/Licencia     New BSD License
 */

namespace ActiveRecord\mysql;

/**
 * Obtiene la descripción de los campos de la tabla
 * 
 * @param PDO $dbh conexion pdo
 * @param string $table nombre de tabla
 * @param string $schema esquema
 * @return array
 */
function metadata($dbh, $table, $schema = null) {
	if (!$schema) {
		$describe = $dbh->query("DESCRIBE `$table`");
	} else {
		$describe = $dbh->query("DESCRIBE `$schema`.`$table`");
	}
	
	$metadata = array();
	foreach ($describe as $value) {	
		$metadata[$value['field']] = array(
			'Type' => $value['type'],
			'Null' => $value['null'] != 'NO',
			'Key' => $value['key'],
			'Default' => $value['default'] != '',
			'Auto' => $value['extra'] == 'auto_increment'
		);
	}
	
	return $metadata;
}
