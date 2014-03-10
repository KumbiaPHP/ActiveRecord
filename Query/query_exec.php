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

namespace ActiveRecord\Query;

/**
 * Ejecuta una consulta
 * 
 * @param string $type tipo de driver
 * @param string $query_function nombre de funcion
 * @param mixed $args argumentos
 * @return mixed
 * @thow KumbiaException
 */
function query_exec($type, $query_function, $args = null) {
	$query_function = "{$type}_{$query_function}";
	
	require_once __DIR__ . "/{$query_function}.php";
	
	$args = \array_slice(\func_get_args(), 2);
	
	return call_user_func_array(__NAMESPACE__ . "\\$query_function", $args);
}
