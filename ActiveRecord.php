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
 
// @see Db
require_once CORE_PATH . 'libs/ActiveRecord/Db.php';
 
/**
 * Implementación de patrón ActiveRecord
 * 
 */
class ActiveRecord
{
	/**
	 * Constructor
	 * 
	 * @param array $data
	 */
	public function __construct($data = null)
	{
		if($data) $this->_dump($data);
	}
	
	/**
	 * Cargar datos al objeto
	 * 
	 * @param array $data
	 */
	private function _dump($data)
	{
		foreach($data as $k => $v) {
			$this->$k = $v;
		}
	}
	
	/**
	 * Alias de los campos
	 * 
	 * @return array
	 */
	public static function alias() 
	{
		return array();
	}
	
	/**
	 * Ejecuta validaciones
	 * 
	 * @param string $action accion que se ejecuta (create, update)
	 * @return boolean
	 */
	protected function _validate($action)
	{
		$result = true;
			
		foreach(self::metadata('Validations') as $v) {
			$validation = \Util::smallcase(array_shift($v));
			
			if(!include_once CORE_PATH . "libs/ActiveRecord/Validations/{$validation}.php") throw new \KumbiaException('No existe la validación ' . \Util::camelcase($validation));
			
			\array_unshift($v, $action, $this);
			$result = \call_user_func_array("\\ActiveRecord\\Validations\\$validation", $v) && $result;
		}
		
		return $result;
	}
	
	/**
	 * Crear registro
	 * 
	 * @param array $data
	 * @return boolean 
	 * @throw PDOException
	 */
	public function create($data = null) 
	{
		if($data) $this->_dump($data);
		
		// Ejecutar validación
		if(!$this->_validate('create')) return false;
		
		// Callback antes de crear
		if(\method_exists($this, '_before') && $this->_before('create') === false) return true; 
		
		$data = array();
		$columns = array();
		$values = array();
		$withDefault = self::metadata('WithDefault');
		$autoFields = self::metadata('AutoFields');
		
		// Preparar consulta
		foreach(self::metadata('FieldsList') as $field) {
			if(isset($this->$field) && $this->$field != '') {
				$data[":$field"] = $this->$field;
				$columns[] = $field;
				$values[] = ":$field";
			} elseif(!\in_array($field, $withDefault) && !\in_array($field, $autoFields)) {
				$columns[] = $field;
				$values[] = 'NULL';
			}
		}
		$columns = \implode(',', $columns);
		$values = \implode(',', $values);
		
		$table = self::getTable();
		if($schema = self::getSchema()) $table = "$schema.$table";
		
		$sql = "INSERT INTO $table ($columns) VALUES ($values)";
		
		if(!self::prepare($sql)->execute($data)) return false;
		
		// Verifica si la PK es autogenerada
		$pk = self::metadata('PK');
		if(!isset($this->$pk) && \in_array($pk, $autoFields)) {
			// Obtiene los metadatos de los campos de tabla
			$type = self::getDatabaseType();
			require_once CORE_PATH . "libs/ActiveRecord/$type/last_insert_id.php";
			$this->$pk = \call_user_func("\\ActiveRecord\\$type\\last_insert_id", self::_dbh(), $pk, self::getTable(), self::getSchema());
		}
		
		// Callback despues de crear
		if(\method_exists($this, '_after')) $this->_after('create');
		
		return true;
	}
	
	/**
	 * Actualizar registro
	 * 
	 * @param array $data
	 * @return boolean 
	 */
	public function update($data = null)
	{
		if($data) $this->_dump($data);
		
		// Ejecutar validación
		if(!$this->_validate('update')) return false;
		
		// Callback antes de actualizar
		if(\method_exists($this, '_before') && $this->_before('update') === false) return true; 
		
		$pk = self::metadata('PK');
		if(!isset($this->$pk) || $this->$pk == '') throw new \KumbiaException('No se ha especificado valor para la clave primaria');
		
		$data = array();
		$set = array();
		
		// Preparar consulta
		foreach(self::metadata('FieldsList') as $field) {
			if(isset($this->$field) && $this->$field != '') {
				$data[":$field"] = $this->$field;
				if($field != $pk) $set[] = "$field = :$field";
			} else {
				$set[] = "$field = NULL";
			}
		}
		$set = \implode(', ', $set);
		
		$table = self::getTable();
		if($schema = self::getSchema()) $table = "$schema.$table";
		
		$sql = "UPDATE $table SET $set WHERE $pk = :$pk";
		
		if(!self::prepare($sql)->execute($data)) return false;
		
		// Callback despues de actualizar
		if(\method_exists($this, '_after')) $this->_after('update'); 
		
		return true;
	}
	
	/**
	 * Guardar registro
	 * 
	 * @param array $data
	 * @return boolean 
	 */
	public function save($data = null)
	{
		if($data) $this->_dump($data);
		
		$pk = self::metadata('PK');
		
		if(!isset($this->$pk) || $this->$pk == '' || !self::exists("$pk = :value", array(':value' => $this->$pk))) return $this->create();
		
		return $this->update();
	}
	
	/**
	 * Actualizar registros
	 * 
	 * @param array $values
	 * @param string $where condiciones
	 * @param array $values valores para condiciones
	 * @return int numero de registros actualizados
	 */
	public static function updateAll($fields, $where = null, $values = null)
	{
		$table = self::getTable();
		if($schema = self::getSchema()) $table = "$schema.$table";
		
		$dbh = self::_dbh();
		$data = array();
		foreach($fields as $k => $v) {
			$k = self::sqlItemSanitize($k);
			$data[] = "$k=" . $dbh->quote($v);
		}
		$data = \implode(', ', $data);
		
		$sql = "UPDATE $table SET $data";
		
		if($where !== null) $sql .= " WHERE $where";
		
		$sth = self::prepare($sql);
		$sth->execute($values);
		
		return $sth->rowCount();
	}
	
	/**
	 * Eliminar registro
	 * 
	 * @param string $pk valor para clave primaria
	 * @return boolean
	 */
	public static function delete($pk)
	{
		$pkField = self::metadata('PK');
		return self::deleteAll("$pkField = :value", array(':value' => $pk)) > 0;
	}
	 
	/**
	 * Eliminar registro
	 * 
	 * @param string $where condiciones
	 * @param array $values
	 * @return int numero de registros eliminados
	 */
	public static function deleteAll($where = null, $values = null)
	{
		$table = self::getTable();
		if($schema = self::getSchema()) $table = "$schema.$table";
		
		$sql = "DELETE FROM $table";
		if($where !== null) $sql .= " WHERE $where";
		
		$sth = self::prepare($sql);
		$sth->execute($values);
		
		return $sth->rowCount();
	}
	 
	/**
	 * Obtiene nombre de tabla
	 * 
	 * @return string
	 */
	public static function getTable()
	{
		return \Util::smallcase(\get_called_class());
	}
	
	/**
	 * Obtiene el schema al que pertenece
	 * 
	 * @return string
	 */
	public static function getSchema()
	{
		return null;
	}
	
	/**
	 * Obtiene la conexión que se utilizará (contenidas en databases.ini)
	 * 
	 * @return string
	 */
	public static function getDatabase()
	{
		$core = \Config::read('config');
		return $core['application']['database'];
	}
	
	/**
	 * Validaciones de campos
	 * 
	 * @return array
	 */
	protected static function _validations()
	{
		return array();
	}
		
	public static function getDatabaseType()
	{
		// Leer la especificación de conexión
		$databases = \Config::read('databases');
		
		$database = self::getDatabase();
		
		if(!isset($databases[$database])) throw new \KumbiaException("No existe la especificación '$database' para conexión a base de datos en databases.ini");
		
        $type = $databases[$database]['type'];
		return $type;
	}
	
	/**
	 * Obtiene metadatos
	 * 
	 * @param string $key tipo de metadatos a obtener
	 * @return mixed
	 */
	public static function metadata($key)
	{
		static $metadata = null;
		
		if(!$metadata || (PRODUCTION && !($metadata = \Cache::driver()->get(\get_called_class(), 'ActiveRecord')))) {
			
			// Obtiene los metadatos de los campos de tabla
			$type = self::getDatabaseType();
			require_once CORE_PATH . "libs/ActiveRecord/$type/metadata.php";
			$fields = \call_user_func("\\ActiveRecord\\$type\\metadata", self::_dbh(), self::getTable(), self::getSchema());
			
			$pk = null;
			$required = array();
			$other = array();
			$unique = array();
			$withDefault = array();
			$auto = array();
			
			$userRequired = \array_filter(self::_validations(), function($v) {
				return $v[1] == 'Required';
			});
			
			$userOther = \array_filter(self::_validations(), function($v) {
				return $v[1] != 'Unique' && $v[1] != 'Required';
			});
			
			$userUnique = \array_filter(self::_validations(), function($v) {
				return $v[1] == 'Unique';
			});
			
			// Función para verificar si existe la validación en los validadores de usuario
			$validationExists = function($validation, $field, $list) {
				foreach($list as $v) {
					if($v[0] == $validation && $v[1] == $field) return true;
				}
				return false;
			};
			
			// Genera validaciones			
			foreach($fields as $field => $m) {	
				if(!$m['Null'] && !$validationExists('Required', $field, $userRequired)) $required[] = array('Required', $field);
				
				if($m['Key'] == 'PRI') { 
					$pk = $field;
					if(!$validationExists('Unique', $field, $userUnique)) $unique[] = array('Unique', $field);
				}
				
				if($m['Key'] == 'UNI' && !$validationExists('Unique', $field, $userUnique)) $unique[] = array('Unique', $field);
				
				if(\substr_compare($m['Type'], 'int', 0, 3, false) == 0 &&
					!$validationExists('Integer', $field, $userOther)) $other[] = array('Integer', $field);
				
				
				if($m['Type'] == 'date' && !$validationExists('Date', $field, $userOther)) $other[] = array('Date', $field);
				
				if(\preg_match('/^numeric|decimal/', $m['Type'], $match)) {
					$other[] = array('Decimal', $field);
				}
				
				if($m['Default']) $withDefault[] = $field;
				
				if($m['Auto']) $auto[] = $field;
			}
			
			// Metadatos de ActiveRecord
			$metadata = array(
				'PK' => $pk,
				'FieldsList' => \array_keys($fields),
				'Fields' => $fields,
				'WithDefault' => $withDefault,
				'AutoFields' => $auto,
				'Validations' => \array_merge($required, $other, $unique)
			);

			// Cachea los metadatos
			if(PRODUCTION) \Cache::driver()->save($metadata, \Config::get('config.application.metadata_lifetime'), \get_called_class(), 'ActiveRecord');
		}
		
		return $metadata[$key];
	}
	
	/**
	 * Obtiene manejador de conexion a la base de datos
	 * 
	 * @param boolean $force forzar nueva conexion PDO
	 * @return PDO
	 */
	protected static function _dbh($force = false) 
	{		
		return Db::get(self::getDatabase(), $force);
	}
	
	/**
	 * Consulta sql preparada
	 * 
	 * @param string $sql
	 * @return PDOStatement
	 * @throw PDOException
	 */
	public static function prepare($sql)
	{
		$sth = self::_dbh()->prepare($sql);
		$class = \get_called_class();
		$sth->setFetchMode(\PDO::FETCH_INTO, new $class);
		return $sth;
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
     * Buscar por clave primaria
     * 
     * @param string $pk valor para clave primaria
     * @param string $fields campos que se desean obtener separados por coma
     * @return ActiveRecord
     */
    public static function find($pk, $fields = '*')
    {
		return self::firstBy(self::metadata('PK'), $pk, $fields);
	}
	
	/**
	 * Obtener la primera coincidencia por el campo indicado
	 * 
	 * @param string $field campo
	 * @param string $value valor
	 * @param string $fields campos que se desean obtener separados por coma
	 * @return ActiveRecord
	 */
	public static function firstBy($field, $value, $fields = '*')
	{
		$field = self::sqlItemSanitize($field);
		return self::first("$field = :value", array(':value' => $value), $fields);
	}
	
	/**
	 * Obtener la primera coincidencia de las condiciones indicadas
	 * 
	 * @param string $field campo
	 * @param string $value valor
	 * @param string $fields campos que se desean obtener separados por coma
	 * @return ActiveRecord
	 */
	public static function first($where = null, $values = null, $fields = '*')
	{
		$table = self::getTable();
		if($schema = self::getSchema()) $table = "$schema.$table";
		
		$sql = "SELECT $fields FROM $table";
		if($where !== null) $sql .= " WHERE $where";
		
		$type = self::getDatabaseType();
		require_once CORE_PATH . "libs/ActiveRecord/$type/limit.php";
		$sql = \call_user_func("\\ActiveRecord\\$type\\limit", $sql, 1);
		
		$sth = self::prepare($sql);
		$sth->execute($values);
		
		return $sth->fetch();
	}
	
	/**
	 * Obtener la primera coincidencia por el campo indicado
	 * 
	 * @param string $field campo
	 * @param string $value valor
	 * @param string $fields campos que se desean obtener separados por coma
	 * @param string $order ordenar por los campos indicados separados por coma
	 * @param string | int $limit máxima cantidad de registros a obtener
	 * @param string | int $offset valor indice desde donde se comienzan a obtener registros
	 * @return PDOStatement
	 */
	public static function all($where = null, $values = null, $fields = '*', $order = null, $limit = null, $offset = null)
	{
		$table = self::getTable();
		if($schema = self::getSchema()) $table = "$schema.$table";
		
		$sql = "SELECT $fields FROM $table";
		if($where !== null) $sql .= " WHERE $where";
		if($order !== null) $sql .= " ORDER BY $order";
		
		if($limit !== null || $offset !== null) {
			$type = self::getDatabaseType();
			require_once CORE_PATH . "libs/ActiveRecord/$type/limit.php";
			$sql = \call_user_func("\\ActiveRecord\\$type\\limit", $sql, $limit, $offset);
		}
		
		$sth = self::prepare($sql);
		$sth->execute($values);
		
		return $sth;
	}
	
	/**
	 * Obtener todas las coincidencias por el campo indicado
	 * 
	 * @param string $field campo
	 * @param string $value valor
	 * @param string $fields campos que se desean obtener separados por coma
	 * @param string $order ordenar por los campos indicados separados por coma
	 * @param string | int $limit máxima cantidad de registros a obtener
	 * @param string | int $offset valor indice desde donde se comienzan a obtener registros
	 * @return PDOStatement
	 */
	public static function allBy($field, $value, $fields = '*', $order = null, $limit = null, $offset = null)
	{
		$field = self::sqlItemSanitize($field);
		return self::all("$field = :value", array(':value' => $value), $fields, $order, $limit, $offset);
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
		return self::first($where, $values, 'COUNT(*) AS count')->count;	
	}
	
	/**
	 * Verifica si existen registros que coincidan con las condiciones indicadas
	 * 
	 * @param string $where condiciones
	 * @param array $values valores
	 * @return boolean
	 */
	public static function exists($where, $values = null)
	{
		return self::count($where, $values)>0;
	}
}
