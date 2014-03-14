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
 * Base del ORM ActiveRecord
 *
 */
class BaseRecord
{
    /**
     * Database por defecto usa default
     *
     * @var string
     */
    public static $database = 'default';

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
    public function __construct(Array $data = array())
    {
         $this->dump($data);
    }

    /**
     * Cargar datos al objeto
     *
     * @param array $data
     */
    protected function dump(Array $data = array())
    {   
        foreach ($data as $k => $v) {
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
     * Verifica que PK este seteado
     * @throw \KumbiaException
     */
    protected function hasPK(){
        $pk = static::getPK();
        if(empty($this->$pk))
            throw new \KumbiaException(__('No se ha especificado valor para la clave primaria'));
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
     * @return string
     */
    public static function getSchema()
    {
        return '';
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
     * Obtiene la conexión que se utilizará (contenidas en databases.php)
     *
     * @return string
     */
    public static function getDatabase()
    {
        return static::$database;
    }

    /**
     * Obtiene metadatos
     *
     * @return Metadata\Metadata
     */
    public static function metadata()
    {
        return Metadata\Metadata::get(static::getDriver(),static::getDatabase(), static::getTable(), static::getSchema());
    }

    /**
     * Obtiene manejador de conexion a la base de datos
     *
     * @param  boolean $force forzar nueva conexion PDO
     * @return \PDO
     */
    protected static function dbh($force = false)
    {
        return Db::get(static::getDatabase(), $force);
    }

    /**
     * Consulta sql preparada
     *
     * @param  string       $sql
     * @return \PDOStatement
     * @throw \PDOException
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
     * @return \PDOStatement
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

    /**
     * Devuelve el nombre del drive PDO utilizado
     * @return string
     */
    public static function getDriver(){
        return self::dbh()->getAttribute(\PDO::ATTR_DRIVER_NAME);
    }
}
