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
 * @subpackage Metadata
 * @copyright  Copyright (c) 2005-2013 Kumbia Team (http://www.kumbiaphp.com)
 * @license    http://wiki.kumbiaphp.com/Licencia     New BSD License
 */

namespace Kumbia\ActiveRecord\Metadata;

/**
 * Metadata de tabla
 *
 */
abstract class Metadata
{

    /**
     * Singleton de metadata
     *
     * @var array
     */
    private static $instances = array();

    /**
     * Descripción de los campos
     *
     * @var array
     */
    protected $fields = array();

    /**
     * Lista de campos
     *
     * @var array
     */
    protected $fieldsList = array();

    /**
     * Clave primaria
     *
     * @var string
     */
    protected $pk;

    /**
     * Campos con valor predeterminado
     *
     * @var array
     */
    protected $withDefault = array();

    /**
     * Campos con valor autogenerado
     *
     * @var array
     */
    protected $autoFields = array();

    /**
     * Obtiene la metadata de la tabla
     *
     * @param  string   $type tipo de controlador
     * @param  string   $database
     * @param  string   $table
     * @param  string   $schema
     * @return Metadata
     */
    public static function get($type, $database, $table, $schema = null)
    {
        if (!isset(self::$instances["$database.$table.$schema"]) || (PRODUCTION && !(self::$instances["$database.$table.$schema"] = \Cache::driver()->get("$database.$table.$schema", 'ActiveRecord.Metadata')))) {
            $class = ucwords($type) . 'Metadata';

            require_once __DIR__ . "/$class.php";

            $class = __NAMESPACE__ . "\\$class";

            self::$instances["$database.$table.$schema"] = new $class($database, $table, $schema);

            // Cachea los metadatos
            if (PRODUCTION)
                \Cache::driver()->save(self::$instances["$database.$table.$schema"], \Config::get('config.application.metadata_lifetime'), "$database.$table.$schema", 'ActiveRecord.Metadata');
        }

        return self::$instances["$database.$table.$schema"];
    }

    /**
     * Constructor
     *
     * @param string $database base de datos
     * @param string $table    tabla
     * @param string $schema   squema
     */
    private function __construct($database, $table, $schema = null)
    {
        $this->fields = $this->queryFields($database, $table, $schema);
        $this->fieldsList = \array_keys($this->fields);

        foreach ($this->fields as $field => $m) {
            if ($m['Key'] == 'PRI')
                $this->pk = $field;
            if ($m['Default'])
                $this->withDefault[] = $field;
            if ($m['Auto'])
                $this->autoFields[] = $field;
        }
    }

    /**
     * Consultar los campos de la tabla en la base de datos
     *
     * @param  string $database base de datos
     * @param  string $table    tabla
     * @param  string $schema   squema
     * @return array
     */
    abstract protected function queryFields($database, $table, $schema = null);

    /**
     * Obtiene la descripción de los campos
     *
     * @return array
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * Obtiene la lista de campos
     *
     * @return array
     */
    public function getFieldsList()
    {
        return $this->fieldsList;
    }

    /**
     * Obtiene la clave primaria
     *
     * @return string
     */
    public function getPK()
    {
        return $this->pk;
    }

    /**
     * Obtiene los campos con valor predeterminado
     *
     * @return array
     */
    public function getWithDefault()
    {
        return $this->withDefault;
    }

    /**
     * Obtiene los campos con valor generado automatico
     *
     * @return array
     */
    public function getAutoFields()
    {
        return $this->autoFields;
    }

}
