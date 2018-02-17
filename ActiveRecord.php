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
 * @copyright  2005 - 2016  Kumbia Team (http://www.kumbiaphp.com)
 * @license    http://wiki.kumbiaphp.com/Licencia     New BSD License
 */

namespace Kumbia\ActiveRecord;

/**
 * Implementación de patrón ActiveRecord con ayudantes de consultas sql.
 */
class ActiveRecord extends LiteRecord implements \JsonSerializable
{
    const BELONG_TO = 1;
    const HAS_MANY = 2;
    const HAS_ONE = 3;

    /**
     * Describe the relationships.
     *
     * @var array
     */
    protected static $relations = [];

    public static function resolver($relations, $obj)
    {
        $model = $relations->model;
        if ($relations->type === self::HAS_MANY) {
            return $model::allBy($relations->via, $obj->pk());
        }

        return $model::first($relations->via, $obj->pk());
    }

    public static function hasMany($name, $class, $via = null)
    {
        $str = strtolower($name);
        $name = static::getTable();
        static::$relations[$str] = (object) [
            'model' => $class,
            'type' => self::HAS_MANY,
            'via' => $via ? $via : "{$name}_id",
        ];
    }

    public static function hasOne($name, $class, $via = null)
    {
        $str = strtolower($name);
        $name = static::getTable();
        static::$relations[$str] = (object) [
            'model' => $class,
            'type' => self::HAS_ONE,
            'via' => $via ? $via : "{$name}_id",
        ];
    }

    /**
     * json_encode() method.
     */
    public function jsonSerialize()
    {
        return $this; //TODO: populate relations before
    }

    public function __get($key)
    {
        if (property_exists($this, $key)) {
            return $this->$key;
        }
        //it's a relationship
        if (isset(static::$relations[$key])) {
            $this->populate($key);

            return $this->$key;
        }

        return null; //TODO: change for error
    }

    protected static function getRelationship($rel)
    {
        if (!isset(static::$relations[$rel])) {
            throw new \RuntimeException("Invalid relationship '$rel'", 500);
        }

        return static::$relations[$rel];
    }

    public function populate($rel)
    {
        $relations = static::getRelationship($rel);
        $this->$rel = static::resolver($relations, $this);
    }

    /**
     * Pagination of Results.
     *
     * @param array $params   [description]
     * @param array $values   [description]
     * @param int   $page     [description]
     * @param int   $per_page [description]
     *
     * @return Paginator [description]
     */
    public static function pagination($params = [], $values = [], $page = 1, $per_page = 10)
    {
        $model = get_called_class();
        unset($params['limit'], $params['offset']);
        $sql = QueryGenerator::select($model::getSource(), $model::getDriver(), $params);

        return new Paginator($model, $sql, $page, $per_page, $values);
    }

    /**
     * Actualizar registros.
     *
     * @param array  $fields
     * @param string $where  condiciones
     * @param array  $values valores para condiciones
     *
     * @return int numero de registros actualizados
     */
    public static function updateAll(array $fields, $where = null, array $values = [])
    {
        if ($values !== null && !is_array($values)) {
            $values = \array_slice(\func_get_args(), 2);
        }
        $sql = QueryGenerator::updateAll(\get_called_class(), $fields, $values, $where);
        $sth = self::prepare($sql);
        $sth->execute($values);

        return $sth->rowCount();
    }

    /**
     * Eliminar registro.
     *
     * @param string        $where  condiciones
     * @param array |string $values valores
     *
     * @return int numero de registros eliminados
     */
    public static function deleteAll($where = null, $values = null)
    {
        $source = static::getSource();
        $sql = QueryGenerator::deleteAll($source, $where);
        $sth = self::query($sql, $values);

        return $sth->rowCount();
    }

    /**
     * Elimina caracteres que podrian ayudar a ejecutar
     * un ataque de Inyeccion SQL.
     *
     * @param string $sqlItem
     *
     * @return string
     * @throw KumbiaException
     */
    public static function sqlItemSanitize($sqlItem)
    {
        $sqlItem = \trim($sqlItem);
        if ($sqlItem !== '' && $sqlItem !== null) {
            $sql_temp = \preg_replace('/\s+/', '', $sqlItem);
            if (!\preg_match('/^[a-zA-Z0-9_\.]+$/', $sql_temp)) {
                throw new \RuntimeException('Se está tratando de ejecutar un SQL peligroso!');
            }
        }

        return $sqlItem;
    }

    /**
     * Obtener la primera coincidencia por el campo indicado.
     *
     * @param string $field  campo
     * @param string $value  valor
     * @param array  $params parametros adicionales
     *                       order: criterio de ordenamiento
     *                       fields: lista de campos
     *                       join: joins de tablas
     *                       group: agrupar campos
     *                       having: condiciones de grupo
     *                       offset: valor offset
     *
     * @return ActiveRecord
     */
    public static function firstBy($field, $value, $params = [])
    {
        $field = self::sqlItemSanitize($field);
        $params['where'] = "$field = ?";

        return self::first($params, $value);
    }

    /**
     * Obtener la primera coincidencia de las condiciones indicadas.
     *
     * @param array $params parametros adicionales
     *                      order: criterio de ordenamiento
     *                      fields: lista de campos
     *                      group: agrupar campos
     *                      join: joins de tablas
     *                      having: condiciones de grupo
     *                      offset: valor offset queda
     * @param array $values valores de busqueda
     *
     * @return ActiveRecord
     */
    public static function first($params = [], $values = [])
    {
        $args = func_get_args();
        /*Reescribe el limit*/
        $args[0]['limit'] = 1;
        $res = self::doQuery($args);

        return $res->fetch();
    }

    /**
     * Obtener todos los registros.
     *
     * @param array $params
     *                      where: condiciones where
     *                      order: criterio de ordenamiento
     *                      fields: lista de campos
     *                      join: joins de tablas
     *                      group: agrupar campos
     *                      having: condiciones de grupo
     *                      limit: valor limit
     *                      offset: valor offset
     * @param array $values valores de busqueda
     *
     * @return \PDOStatement
     */
    public static function all($params = [], $values = [])
    {
        $res = self::doQuery(func_get_args());

        return $res->fetchAll();
    }

    /**
     * Do a query.
     *
     * @param array $array params of query
     *
     * @return \PDOStatement|false
     */
    protected static function doQuery(array $array)
    {
        $params = self::getParam($array);
        $values = self::getValues($array);
        $sql = QueryGenerator::select(static::getSource(), static::getDriver(), $params);
        $sth = static::query($sql, $values);

        return $sth;
    }

    /**
     * Retorna los parametros para el doQuery.
     *
     * @param array $array
     *
     * @return array
     */
    protected static function getParam(array &$array)
    {
        $val = array_shift($array);

        return is_null($val) ? [] : $val;
    }

    /**
     * Retorna los values para el doQuery.
     *
     * @param array $array
     *
     * @return array
     */
    protected static function getValues(array $array)
    {
        return isset($array[0]) ?
        is_array($array[0]) ? $array[0] : [$array[0]] : $array;
    }

    /**
     * Obtener todas las coincidencias por el campo indicado.
     *
     * @param string $field  campo
     * @param string $value  valor
     * @param array  $params
     *                       order: criterio de ordenamiento
     *                       fields: lista de campos
     *                       join: joins de tablas
     *                       group: agrupar campos
     *                       having: condiciones de grupo
     *                       limit: valor limit
     *                       offset: valor offset
     *
     * @return \PDOStatement
     */
    public static function allBy($field, $value, $params = [])
    {
        $field = self::sqlItemSanitize($field);
        $params['where'] = "$field = ?";

        return self::all($params, $value);
    }

    /**
     * Cuenta los registros que coincidan con las condiciones indicadas.
     *
     * @param string $where  condiciones
     * @param array  $values valores
     *
     * @return int
     */
    public static function count($where = null, $values = null)
    {
        $source = static::getSource();
        $sql = QueryGenerator::count($source, $where);
        if ($values !== null && !is_array($values)) {
            $values = \array_slice(\func_get_args(), 1);
        }
        $sth = static::query($sql, $values);

        return $sth->fetch()->count;
    }

    /**
     * Paginar.
     *
     * @param array $params
     * @param int   $page    numero de pagina
     * @param int   $perPage cantidad de items por pagina
     * @param array $values  valores
     *
     * @return Paginator
     */
    public static function paginate(array $params, $page, $perPage, $values = null)
    {
        unset($params['limit'], $params['offset']);
        $sql = QueryGenerator::select(static::getSource(), static::getDriver(), $params);

        // Valores para consulta
        if ($values !== null && !\is_array($values)) {
            $values = \array_slice(func_get_args(), 3);
        }

        return new Paginator(\get_called_class(), $sql, (int) $page, (int) $perPage, $values);
    }

    /**
     * Obtiene todos los registros de la consulta sql.
     *
     * @param string         $sql
     * @param string | array $values
     *
     * @return array
     */
    public static function allBySql($sql, $values = null)
    {
        if (!is_array($values)) {
            $values = \array_slice(\func_get_args(), 1);
        }

        return parent::all($sql, $values);
    }

    /**
     * Obtiene el primer registro de la consulta sql.
     *
     * @param string         $sql
     * @param string | array $values
     *
     * @return array
     */
    public static function firstBySql($sql, $values = null)
    {
        if (!is_array($values)) {
            $values = \array_slice(\func_get_args(), 1);
        }

        return parent::first($sql, $values);
    }
}
