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
 * @subpackage Validations
 * @copyright  Copyright (c) 2005-2013 Kumbia Team (http://www.kumbiaphp.com)
 * @license    http://wiki.kumbiaphp.com/Licencia     New BSD License
 */

namespace ActiveRecord\Validations;

/**
 * Validación para campo numérico decimal
 * 
 * @param string $action accion (create, update)
 * @param ActiveRecord $model objeto activerecord
 * @param string $field nombre de campo a validar
 * @param string $fieldName nombre de campo a mostrar
 * @param string $message mensaje a mostrar
 * @param string $decimal separador decimal (predeterminado .)
 * @return boolean
 */
function decimal($action, $model, $field, $fieldName = null, $message = null, $decimal = '.') {
	if(!isset($model->$field) || $model->$field == ''
		|| filter_var($model->$field, FILTER_VALIDATE_FLOAT, array("options"=>array('decimal' => $decimal)))) return true;
	
	if(!$message) {
		if(!$fieldName) {
			$alias = $model->alias();
			$fieldName = isset($alias[$field]) ? $alias[$field] : \ucwords(\Util::humanize($field));
		}
		
		$message = "$fieldName debe ser un número decimal";
	}
		
	\Flash::error($message);
	return false;
}
