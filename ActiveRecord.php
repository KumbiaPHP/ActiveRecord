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
 
// @see LiteRecord
require_once __DIR__ . '/LiteRecord.php';
 
/**
 * Implementación de patrón ActiveRecord con ayudantes de consultas sql
 * 
 */
class ActiveRecord extends LiteRecord
{
	/**
	 * Actualizar registros
	 * 
	 * @param array $values
	 * @param string $where condiciones
	 * @param array | string $values valores para condiciones
	 * @return int numero de registros actualizados
	 */
	public static function updateAll($fields, $where = null, $values = null)
	{
		$dbh = self::_dbh();
		$data = array();
		foreach($fields as $k => $v) {
			$k = self::sqlItemSanitize($k);
			$data[] = "$k=" . $dbh->quote($v);
		}
		$data = \implode(', ', $data);
	
		$source = self::getSource();
		
		$sql = "UPDATE $source SET $data";
		
		if($where !== null) $sql .= " WHERE $where";
		
		$sth = self::prepare($sql);
		
		if($values !== null && !is_array($values)) $values = \array_slice(\func_get_args(), 2);
		
		$sth->execute($values);
		
		return $sth->rowCount();
	}
		 
	/**
	 * Eliminar registro
	 * 
	 * @param string $where condiciones
	 * @param array |string  $values valores
	 * @return int numero de registros eliminados
	 */
	public static function deleteAll($where = null, $values = null)
	{
		$source = self::getSource();
		
		$sql = "DELETE FROM $source";
		if($where !== null) $sql .= " WHERE $where";
		
		$sth = self::prepare($sql);
		
		if($values !== null && !is_array($values)) $values = \array_slice(\func_get_args(), 1);
		
		$sth->execute($values);
		
		return $sth->rowCount();
	}
	
	/**
     * Elimina caracteres que podrian ayudar a ejecutar
     * un ataque de Inyeccion SQL
     *
     * @param string $sqlItem
     * @return string
     * @throw KumbiaException
     */
    public static function sqlItemSanitize($sqlItem)
    {
        $sqlItem = \trim($sqlItem);
        if ($sqlItem !== '' && $sqlItem !== null) {
            $sql_temp = \preg_replace('/\s+/', '', $sqlItem);
            if (!\preg_match('/^[a-zA-Z0-9_\.]+$/', $sql_temp)) {
                throw new \KumbiaException('Se esta tratando de ejecutar una operacion maliciosa!');
            }
        }
        return $sqlItem;
    }
	
	/**
	 * Obtener la primera coincidencia por el campo indicado
	 * 
	 * @param string $field campo
	 * @param string $value valor
	 * @param array $params parametros adicionales
	 *      order: criterio de ordenamiento
	 *      fields: lista de campos
	 *      join: joins de tablas
	 *      group: agrupar campos
	 *      having: condiciones de grupo
	 *      offset: valor offset 
	 * @return ActiveRecord
	 */
	public static function firstBy($field, $value, $params = array())
	{
		$field = self::sqlItemSanitize($field);
		$params['where'] = "$field = ?";
		return self::first($params, $value);
	}
	
	/**
	 * Obtener la primera coincidencia de las condiciones indicadas
	 * 
	 * @param array $params parametros de bus
	 * @param string $field campo
	 * @param string $value valor
	 * @param array $params parametros adicionales
	 *      order: criterio de ordenamiento
	 *      fields: lista de campos
	 *      group: agrupar campos
	 *      join: joins de tablas
	 *      having: condiciones de grupo
	 *      offset: valor offset queda
	 * @param array $values valores de busqueda
	 * @return ActiveRecord
	 */
	public static function first($params = array(), $values = null)
	{
		if($values !== null && !is_array($values)) $values = \array_slice(\func_get_args(), 1);
		$params['limit'] = 1;
		return self::all($params, $values)->fetch();
	}
	
	/**
	 * Obtener todos los registros
	 * 
	 * @param array $params
	 *      where: condiciones where
	 *      order: criterio de ordenamiento
	 *      fields: lista de campos
	 *      join: joins de tablas
	 *      group: agrupar campos
	 *      having: condiciones de grupo
	 *      limit: valor limit
	 *      offset: valor offset 
	 * @param array $values valores de busqueda
	 * @return PDOStatement
	 */
	public static function all($params = null, $values = null)
	{
		if($params === null) {
			$source = self::getSource();
			$sql = "SELECT * FROM $source";
		} else {
			$sql = self::_buildSelect($params);
		}
		
		$sth = self::prepare($sql);
		
		if($values !== null && !is_array($values)) $values = \array_slice(\func_get_args(), 1);
		
		$sth->execute($values);
		
		return $sth;
	}
	
	/**
	 * Construye una consulta select desde una lista de parametros
	 * 
	 * @param array $params parametros de consulta select
	 *      where: condiciones where
	 *      order: criterio de ordenamiento
	 *      fields: lista de campos
	 *      join: joins de tablas
	 *      group: agrupar campos
	 *      having: condiciones de grupo
	 *      limit: valor limit
	 *      offset: valor offset 
	 * @return string
	 */
	protected static function _buildSelect($params)
	{
		$source = self::getSource();
		
		if(!isset($params['fields'])) $params['fields'] = '*';
		
		$sql = "SELECT {$params['fields']} FROM $source";
		
		if(isset($params['join'])) $sql .= " {$params['join']}";
		if(isset($params['where'])) $sql .= " WHERE {$params['where']}";
		if(isset($params['group'])) $sql .= " GROUP BY {$params['group']}";
		if(isset($params['having'])) $sql .= " HAVING {$params['having']}";
		if(isset($params['order'])) $sql .= " ORDER BY {$params['order']}";
		
		$limit = isset($params['limit']) ? $params['limit'] : null;
		$offset = isset($params['offset']) ? $params['offset'] : null;
		
		if($limit !== null || $offset !== null) {
			require_once __DIR__ . '/Query/query_exec.php';
			$sql = Query\query_exec(self::getDatabase(), 'limit', $sql, $limit, $offset);
		}
		
		return $sql;
	}
	
	/**
	 * Obtener todas las coincidencias por el campo indicado
	 * 
	 * @param string $field campo
	 * @param string $value valor
	 * @param array $params
	 *      order: criterio de ordenamiento
	 *      fields: lista de campos
	 *      join: joins de tablas
	 *      group: agrupar campos
	 *      having: condiciones de grupo
	 *      limit: valor limit
	 *      offset: valor offset 
	 * @return PDOStatement
	 */
	public static function allBy($field, $value, $params = array())
	{
		$field = self::sqlItemSanitize($field);
		$params['where'] = "$field = ?";
		return self::all($params, $value);
	}
	
	/**
	 * Cuenta los registros que coincidan con las condiciones indicadas
	 * 
	 * @param string $where condiciones
	 * @param array $values valores
	 * @return int
	 */
	public static function count($where = null, $values = null)
	{		
		$source = self::getSource();
		
		$sql = "SELECT COUNT(*) AS count FROM $source";
		if($where !== null) $sql .= " WHERE $where";
		
		$sth = self::prepare($sql);
		
		if($values !== null && !is_array($values)) $values = \array_slice(\func_get_args(), 1);
		
		$sth->execute($values);
		
		return $sth->fetch()->count;	
	}
	
	/**
	 * Paginar
	 * 
     * @param array $params
     * @param int $page numero de pagina
     * @param int $perPage cantidad de items por pagina
     * @param array $values valores
     * @return Paginator
	 */
	public static function paginate($params, $page, $perPage, $values = null)
	{
		unset($params['limit'], $params['offset']);
		$sql = self::_buildSelect($params);
		
		// Valores para consulta
        if($values !== null && !\is_array($values)) $values = \array_slice(func_get_args(), 3);
        
        require_once __DIR__ . '/Paginator.php';
        return new Paginator(\get_called_class(), $sql, $page, $perPage, $values);
	}
}
