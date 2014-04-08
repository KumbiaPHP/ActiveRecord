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
 * @copyright  Copyright (c) 2005-2014 Kumbia Team (http://www.kumbiaphp.com)
 * @license    http://wiki.kumbiaphp.com/Licencia     New BSD License
 */

namespace Kumbia\ActiveRecord;

/**
 * Implementación de patrón ActiveRecord sin ayudantes de consultas SQL
 *
 */
class LiteRecord extends BaseRecord
{

    /**
    * Obtener objeto por clave primaria, $var = Modelo($id)
    *
    * @param string $id valor para clave primaria
    * @return ActiveRecord
    */
    public function __invoke($id)
    {
        return self::get($id);
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
    public function create(Array $data = array())
    {
        $this->dump($data);

        // Callback antes de crear
        if($this->callback('_beforeCreate') === false) return false;

        $sql = QueryGenerator::insert($this, $data);

        if(!self::prepare($sql)->execute($data)) return false;

        // Verifica si la PK es autogenerada
        $pk = static::getPK();
        if (!isset($this->$pk)) {
            $this->$pk = QueryGenerator::query(static::getDriver(), 'last_insert_id', self::dbh(), $pk, static::getTable(), static::getSchema());
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
    public function update(Array $data = array())
    {
        $this->dump($data);
        // Callback antes de actualizar
        if($this->callback('_beforeUpdate') === false) return false;
        $this->hasPK();
        $values = array();
        $sql = QueryGenerator::update($this, $values);
        //var_dump($values);var_dump($sql);die;
        if(!self::prepare($sql)->execute($values)) return false;
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
    public function save(Array $data = array())
    {
        $this->dump($data);

        if($this->callback('_beforeSave') === false) return false;

        $method = $this->saveMethod();
        $result = $this->$method();

        if(!$result) return false;

        $this->callback('_afterSave');

        return true;
    }

    /**
     * Retorna el nombre del metodo a llamar durante un save (create o update)
     * @return string
     */
    protected function saveMethod(){
        $pk = static::getPK();
        return (empty($this->$pk) || !static::exists($this->$pk)) ?
            'create' : 'update';
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
     * Todos los registros donde el campo = value
     *
     * @param  string       $field  Campo
     * @param  string       $field  Valor
     * @param  string       $fields Campos que se desean obtener separados por coma
     * @return LiteRecord
     */
    public static function allBy($field, $value, $fields = '*')
    {
        return self::by($field, $value, $fields)->fetchAll();
    }
    
    /**
     * Primer registro donde el campo = value
     *
     * @param  string       $field  Campo
     * @param  string       $field  Valor
     * @param  string       $fields Campos que se desean obtener separados por coma
     * @return LiteRecord
     */
    public static function firstBy($field, $value, $fields = '*')
    {
        return self::by($field, $value, $fields)->fetch();
    }
    
    /**
     * Devuelve PDOStatement donde el campo = value
     *
     * @param  string       $field  Campo
     * @param  string       $field  Valor
     * @param  string       $fields Campos que se desean obtener separados por coma
     * @return \PDOStatement
     */
    public static function by($field, $value, $fields = '*')
    {
        $source = static::getSource();

        $sql = "SELECT $fields FROM $source where ? = ?";

        return self::query($sql, $field, $value);
    }

}