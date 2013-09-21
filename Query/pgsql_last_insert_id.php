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
 * @subpackage Query
 * @copyright  Copyright (c) 2005-2013 Kumbia Team (http://www.kumbiaphp.com)
 * @license    http://wiki.kumbiaphp.com/Licencia     New BSD License
 */

namespace ActiveRecord\Query;

/**
 * Obtiene el último id generado en pgsql
 * 
 * @param PDO $dbh conexion pdo
 * @param string $pk campo clave primaria
 * @param string $table nombre de tabla
 * @param string $schema esquema
 * @return int
 */
function pgsql_last_insert_id($dbh, $pk, $table, $schema = null) {
	$seq = "{$table}_{$pk}_seq";
	if($schema) $seq = "$schema.$seq";
	return $dbh->lastInsertId($seq);
}
