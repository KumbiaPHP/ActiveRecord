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
 * @subpackage pgsql
 * @copyright  Copyright (c) 2005-2013 Kumbia Team (http://www.kumbiaphp.com)
 * @license    http://wiki.kumbiaphp.com/Licencia     New BSD License
 */

namespace ActiveRecord\pgsql;

/**
 * Obtiene la descripción de los campos de la tabla
 * 
 * @param PDO $dbh conexion pdo
 * @param string $table nombre de tabla
 * @param string $schema esquema
 * @return array
 */
function metadata($dbh, $table, $schema = null) {
	
	if(!$schema) $schema = 'public';
	
	// Nota: Se excluyen claves compuestas
	$describe = $dbh->query("
				SELECT DISTINCT
			c.column_name AS field,
			c.udt_name AS type,
			tc.constraint_type AS key,
			c.column_default AS default,
			c.is_nullable AS null
		FROM information_schema.columns c
		LEFT OUTER JOIN information_schema.key_column_usage cu ON (
			cu.column_name = c.column_name AND cu.table_name = c.table_name AND (
				SELECT COUNT(*) FROM information_schema.key_column_usage
				WHERE constraint_name = cu.constraint_name
			) = 1)
		LEFT OUTER JOIN information_schema.table_constraints tc ON (cu.constraint_name = tc.constraint_name AND tc.constraint_type IN ('PRIMARY KEY', 'UNIQUE'))
		WHERE c.table_name = '$table' AND c.table_schema = '$schema';
	");
	
	$metadata = array();
	foreach ($describe as $value) {	
		
		$metadata[$value['field']] = array(
			'Type' => $value['type'],
			'Null' => $value['null'] != 'NO',
			'Default' => $value['default'] != '',
			'Key' => \substr($value['key'], 0, 3),
			'Auto' => \preg_match('/^nextval\(/', $value['default'])
		);
	}
	
	return $metadata;
}
