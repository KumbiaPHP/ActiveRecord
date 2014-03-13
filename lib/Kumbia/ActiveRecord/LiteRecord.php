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

namespace Kumbia\ActiveRecord;

/**
 * Implementación de patrón ActiveRecord sin ayudantes de consultas SQL
 *
 */
class LiteRecord
{
    /**
     * PK por defecto, si no existe mira en metadata
     *
     * @var string
     */
    public static $pk = 'id';

    /**
     * Prefijo para el nombre de tabla
     *
     * @var string
     */
    public static $prefix;

    /**
     * Constructor
     *
     * @param array $data
     */
    public function __construct($data = null)
    {
        if($data) $this->dump($data);
    }

    /**
     * Cargar datos al objeto
     *
     * @param array $data
     */
    protected function dump($data)
    {
        foreach ($data as $k => $v) {
            $this->$k = $v;
        }
    }

    /**
    * Obtener objeto por clave primaria, $var = $Modelo($id)
    *
    * @param string $pk valor para clave primaria
    * @return ActiveRecord
    */
    public function __invoke($id)
    {
        return self::get($id);
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
     * Invoca el callback
     *
     * @param  string $callback
     * @return mixed
     */
    protected function callback($callback)
    {
        if(\method_exists($this, $callback)) return $this->$callback();

        return null;
    }

    /**
     * Crear registro
     *
     * @param  array   $data
     * @return boolean
     * @throw PDOException
     */
    public function create($data = null)
    {
        if($data) $this->dump($data);

        // Callback antes de crear
        if($this->callback('_beforeCreate') === false) return false;

        $data = array();
        $columns = array();
        $values = array();
        $withDefault = self::metadata()->getWithDefault();
        $autoFields = self::metadata()->getAutoFields();

        // Preparar consulta
        foreach (self::metadata()->getFieldsList() as $field) {
            if (isset($this->$field) && $this->$field != '') {
                $data[":$field"] = $this->$field;
                $columns[] = $field;
                $values[] = ":$field";
            } elseif (!\in_array($field, $withDefault) && !\in_array($field, $autoFields)) {
                $columns[] = $field;
                $values[] = 'NULL';
            }
        }
        $columns = \implode(',', $columns);
        $values = \implode(',', $values);

        $source = static::getSource();
        $sql = "INSERT INTO $source ($columns) VALUES ($values)";

        if(!self::prepare($sql)->execute($data)) return false;

        // Verifica si la PK es autogenerada
        $pk = static::getPK();
        if (!isset($this->$pk) && \in_array($pk, $autoFields)) {
            $type = Db::get(static::getDatabase())->getAttribute(\PDO::ATTR_DRIVER_NAME);
            $this->$pk = Query\query_exec($type, 'last_insert_id', self::dbh(), $pk, static::getTable(), static::getSchema());
        }

        // Callback despues de crear
        $this->callback('_afterCreate');

        return true;
    }

    /**
     * Actualizar registro
     *
     * @param  array   $data
     * @return boolean
     */
    public function update($data = null)
    {
        if($data) $this->dump($data);

        // Callback antes de actualizar
        if($this->callback('_beforeUpdate') === false) return false;

        $pk = static::getPK();
        if(!isset($this->$pk) || $this->$pk === '') throw new \KumbiaException(__('No se ha especificado valor para la clave primaria'));

        $data = array();
        $set = array();

        // Preparar consulta
        foreach (self::metadata()->getFieldsList() as $field) {
            if (isset($this->$field) && $this->$field != '') {
                $data[":$field"] = $this->$field;
                if($field != $pk) $set[] = "$field = :$field";
            } else {
                $set[] = "$field = NULL";
            }
        }
        $set = \implode(', ', $set);

        $source = static::getSource();

        $sql = "UPDATE $source SET $set WHERE $pk = :$pk";

        if(!self::prepare($sql)->execute($data)) return false;

        // Callback despues de actualizar
        $this->callback('_afterUpdate');

        return true;
    }

    /**
     * Guardar registro
     *
     * @param  array   $data
     * @return boolean
     */
    public function save($data = null)
    {
        if($data) $this->dump($data);

        if($this->callback('_beforeSave') === false) return false;

        $pk = static::getPK();

        if (!isset($this->$pk) || $this->$pk === '' || !self::exists($this->$pk)) {
            $result = $this->create();
        } else {
            $result = $this->update();
        }

        if(!$result) return false;

        $this->callback('_afterSave');

        return true;
    }

    /**
     * Eliminar registro por pk
     *
     * @param  int     $pk valor para clave primaria
     * @return boolean
     */
    public static function delete($pk)
    {
        $source = static::getSource();
        $pkField = static::getPK();

        return self::query("DELETE FROM $source WHERE $pkField = ?", (int) $pk)->rowCount() > 0;
    }

    /**
     * Obtiene la llave primaria
     *
     * @return string
     */
    public static function getPK()
    {
        if(static::$pk) return static::$pk;

        return self::metadata()->getPK();
    }

    /**
     * Obtiene nombre de tabla
     *
     * @return string
     */
    public static function getTable()
    {
        return self::smallcase(\get_called_class());
    }

    /**
     * Obtiene el schema al que pertenece
     *
     * @return string|null
     */
    public static function getSchema()
    {
        return null;
    }

    /**
     * Obtiene la combinación de esquema y tabla
     *
     * @return string
     */
    public static function getSource()
    {
        $source = static::getTable();
        if($schema = static::getSchema()) $source = "$schema.$source";

        return $source;
    }

    /**
     * Obtiene la conexión que se utilizará (contenidas en databases.ini)
     *
     * @return string
     */
    public static function getDatabase()
    {
        return 'default';
    }

    /**
     * Obtiene metadatos
     *
     * @return Metadata
     */
    public static function metadata()
    {
        // Obtiene metadata
        $type = Db::get(static::getDatabase())->getAttribute(\PDO::ATTR_DRIVER_NAME);
        return Metadata\Metadata::get($type,static::getDatabase(), static::getTable(), static::getSchema());
    }

    /**
     * Obtiene manejador de conexion a la base de datos
     *
     * @param  boolean $force forzar nueva conexion PDO
     * @return PDO
     */
    protected static function dbh($force = false)
    {
        return Db::get(static::getDatabase(), $force);
    }

    /**
     * Consulta sql preparada
     *
     * @param  string       $sql
     * @return PDOStatement
     * @throw PDOException
     */
    public static function prepare($sql)
    {
        $sth = self::dbh()->prepare($sql);
        $sth->setFetchMode(\PDO::FETCH_CLASS, \get_called_class());

        return $sth;
    }

    /**
     * Consulta sql
     *
     * @param  string       $sql
     * @return LiteRecord
     * @throw PDOException
     */
    public static function sql($sql)
    {
        $sth = self::dbh()->query($sql);
        $sth->setFetchMode(\PDO::FETCH_CLASS, \get_called_class());

        return $sth;
    }

    /**
     * Ejecuta consulta sql
     *
     * @param  string         $sql
     * @param  array | string $values valores
     * @return PDOStatement
     */
    public static function query($sql, $values = NULL)
    {
        if (func_num_args() === 1) return self::sql($sql);

        $sth = self::prepare($sql);
        if (!is_array($values)) {
            $values = \array_slice(\func_get_args(), 1);
        }

        return $sth->execute($values) ? $sth : FALSE;
    }

    /**
     * Buscar por clave primaria
     *
     * @param  string       $pk     valor para clave primaria
     * @param  string       $fields campos que se desean obtener separados por coma
     * @return LiteRecord
     */
    public static function get($pk, $fields = '*')
    {
        $source = static::getSource();
        $pkField = static::getPK();

        $sql = "SELECT $fields FROM $source WHERE $pkField = ?";

        return self::query($sql, $pk)->fetch();
    }

    /**
     * Listar todos los registros
     *
     * @param  string       $fields campos que se desean obtener separados por coma
     * @return LiteRecord
     */
    public static function all($fields = '*')
    {
        $source = static::getSource();

        $sql = "SELECT $fields FROM $source";

        return self::sql($sql)->fetchAll();
    }

    /**
     * Verifica si existe el registro
     *
     * @param  string  $pk valor para clave primaria
     * @return boolean
     */
    public static function exists($pk)
    {
        $source = static::getSource();
        $pkField = static::getPK();

        return self::query("SELECT COUNT(*) AS count FROM $source WHERE $pkField = ?", $pk)->fetch()->count > 0;
    }

    /**
     * Paginar consulta sql
     *
     * @param  string    $sql     consulta select sql
     * @param  int       $page    numero de pagina
     * @param  int       $perPage cantidad de items por pagina
     * @param  array     $values  valores
     * @return Paginator
     */
    public static function paginateQuery($sql, $page, $perPage, $values = array())
    {
        return new Paginator(\get_called_class(), $sql, (int) $page, (int) $perPage, $values);
    }

    /**
     * Convierte la cadena CamelCase en notacion smallcase
     * @param  string $s cadena a convertir
     * @return string
     *                  */
    public static function smallcase($s)
    {
        return strtolower(preg_replace('/([A-Z])/', "_\\1", lcfirst($s)));
    }
}
