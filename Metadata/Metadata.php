<?php

/**
 * KumbiaPHP web & app Framework.
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
 *
 * @copyright  2005 - 2020  Kumbia Team (http://www.kumbiaphp.com)
 * @license    http://wiki.kumbiaphp.com/Licencia     New BSD License
 */
namespace Kumbia\ActiveRecord\Metadata;

/**
 * Metadata de tabla.
 */
abstract class Metadata
{
    /**
     * Singleton de metadata.
     *
     * @var self[]
     */
    private static $instances = [];

    /**
     * Descripción de los campos.
     *
     * @var string[]
     */
    protected $fields = [];

    /**
     * Lista de campos.
     *
     * @var string[]
     */
    protected $fieldsList = [];

    /**
     * Clave primaria.
     *
     * @var string
     */
    protected $pk;

    /**
     * Campos con valor predeterminado.
     *
     * @var string[]
     */
    protected $withDefault = [];

    /**
     * Campos con valor autogenerado.
     *
     * @var string[]
     */
    protected $autoFields = [];

    /**
     * Metadata de la tabla.
     *
     * @param  string     $type       tipo de controlador
     * @param  string     $database
     * @param  string     $table
     * @param  string     $schema
     * 
     * @return self
     */
    public static function get(string $type, string $database, string $table, string $schema = ''): self
    {
        return self::$instances["$database.$table.$schema"] ?? self::getMetadata($type, $database, $table, $schema);
    }

    /**
     * Obtiene la metadata de la tabla
     * Y la cachea si esta en producción.
     *
     * @param  string     $type       tipo de controlador
     * @param  string     $database
     * @param  string     $table
     * @param  string     $schema
     * 
     * @return self
     */
    private static function getMetadata(string $type, string $database, string $table, string $schema): self
    {
        $key = "$database.$table.$schema";
        if (\PRODUCTION && ! (self::$instances[$key] = \Cache::driver()->get($key, 'ActiveRecord.Metadata'))) {
            return self::$instances[$key];
        }
        $class = \ucwords($type).'Metadata';

        $class = __NAMESPACE__."\\$class";

        self::$instances[$key] = new $class($database, $table, $schema);

        // Cachea los metadatos
        if (\PRODUCTION) {
            \Cache::driver()->save(
                self::$instances[$key],
                \Config::get('config.application.metadata_lifetime'),
                $key,
                'ActiveRecord.Metadata'
            );
        }

        return self::$instances[$key];
    }

    /**
     * Constructor.
     *
     * @param string $database base de datos
     * @param string $table    tabla
     * @param string $schema   squema
     */
    private function __construct(string $database, string $table, string $schema = '')
    {
        $this->fields     = $this->queryFields($database, $table, $schema);
        $this->fieldsList = \array_keys($this->fields);
    }

    /**
     * Permite el filtrado de columna en PK, por Defecto y Autogenerado.
     *
     * @param array     $meta  información de la columna
     * @param string    $field nombre      de la columna
     */
    protected function filterCol(array $meta, string $field): void
    {
        if ($meta['Key'] === 'PRI') {
            $this->pk = $field;
        } elseif ($meta['Default']) {
            $this->withDefault[] = $field;
        } elseif ($meta['Auto']) {
            $this->autoFields[] = $field;
        }
    }

    /**
     * Consultar los campos de la tabla en la base de datos.
     *
     * @param  string  $database base de datos
     * @param  string  $table    tabla
     * @param  string  $schema   squema
     * 
     * @return array
     */
    abstract protected function queryFields(string $database, string $table, string $schema = ''): array;

    /**
     * Obtiene la descripción de los campos.
     *
     * @return string[]
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * Obtiene la lista de campos.
     *
     * @return string[]
     */
    public function getFieldsList(): array
    {
        return $this->fieldsList;
    }

    /**
     * Obtiene la clave primaria.
     *
     * @return string
     */
    public function getPK(): string
    {
        return $this->pk;
    }

    /**
     * Obtiene los campos con valor predeterminado.
     *
     * @return string[]
     */
    public function getWithDefault(): array
    {
        return $this->withDefault;
    }

    /**
     * Obtiene los campos con valor generado automatico.
     *
     * @return string[]
     */
    public function getAutoFields(): array
    {
        return $this->autoFields;
    }
}
