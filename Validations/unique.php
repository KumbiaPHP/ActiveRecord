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
 * Validación para campo unico
 * 
 * @param string $action accion (create, update)
 * @param ActiveRecord $model objeto activerecord
 * @param string | array $field nombre de campo a validar o array de campos que componen la clave unica
 * @param string | array $fieldName nombre de campo a mostrar o array de nombres de campos
 * @param string $message mensaje a mostrar
 * @return boolean
 * @throw KumbiaException
 */
function unique($action, $model, $field, $fieldName = null, $message = null) {
	if(\is_array($field)) { // Clave única compuesta
		
		$where = array();
		$values = array();
		foreach($field as $f) {
			if(!isset($model->$f) || $model->$f == '') return true;
			$where[] = "$f = :$f";
			$values[":$f"] = $model->$f;
		}
		$where = \implode(' AND ', $where);
		
		
		if($action == 'create') { // Crear
			if(!$model->exists($where, $values)) return true;
		} else { // Actualizar
			
			$pk = \call_user_func(array(\get_class($model), 'metadata'), 'PK');
			
			if(!isset($model->$pk) || $model->$pk == '') throw new \KumbiaException("Valor no asignado para la clave primaria $pk");
			
			$values[":$pk"] = $model->$pk;
			
			if(!$model->exists("$where AND NOT $pk = :$pk", $values)) return true;
		}
		
		if(!$message) {
			$n = \count($field);
			$alias = $model->alias();
		
			if(!$fieldName) {
				for($i = 0; $i < $n; $i++) {
					$fields[] = isset($alias[$field[$i]]) ? $alias[$field[$i]] : \ucwords(\Util::humanize($field[$i]));
				}
			} else {
				for($i = 0; $i < $n; $i++) {
					if(isset($fieldName[$i])) {
						$fields[] = $fieldName[$i];
					} else {
						$fields[] = isset($alias[$field[$i]]) ? $alias[$field[$i]] : \ucwords(\Util::humanize($field[$i]));
					}
				}
			}
	
			$fields = implode(', ', $fields);
			
			$message = "Ya existe un registro con los valores indicados en $fields";
		}
		
	} else { // Clave única simple 
		
		if(!isset($model->$field) || $model->$field == '') return true;
		
		if($action == 'create') { // Crear
			if(!$model->exists("$field = :value", array(':value' => $model->$field))) return true;
		} else { // Actualizar
			$pk = \call_user_func(array(\get_class($model), 'metadata'), 'PK');
			
			if(!isset($model->$pk) || $model->$pk == '') throw new \KumbiaException("Valor no asignado para la clave primaria $pk");
			
			if(!$model->exists("$field = :$field AND NOT $pk = :$pk", array(":$field" => $model->$field, ":$pk" => $model->$pk))) return true;
		}
		
		if(!$message) {
			if(!$fieldName) {
				$alias = $model->alias();
				$fieldName = isset($alias[$field]) ? $alias[$field] : \ucwords(\Util::humanize($field));
			}
			
			$message = "Ya existe un registro con el valor indicado en $fieldName";
		}
	}
			
	\Flash::error($message);
	return false;
}
