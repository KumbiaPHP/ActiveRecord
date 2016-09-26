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
 * Generador de codigo SQL.
 */
class QueryGenerator
{
    /**
     * Construye una consulta select desde una lista de parametros.
     *
     * @param array  $params parametros de consulta select
     *                       where: condiciones where
     *                       order: criterio de ordenamiento
     *                       fields: lista de campos
     *                       join: joins de tablas
     *                       group: agrupar campos
     *                       having: condiciones de grupo
     *                       limit: valor limit
     *                       offset: valor offset
     * @param string $source
     * @param string $type
     *
     * @return string
     */
    public static function select($source, $type, array $params)
    {
        $params = array_merge([
            'fields' => '*',
            'join'   => '',
            'limit'  => null,
            'offset' => null,
            'where'  => null,
            'group'  => null,
            'having' => null,
            'order'  => null,
        ], $params);

        list($where, $group, $having, $order) = static::prepareParam($params);
        $sql = "SELECT {$params['fields']} FROM $source {$params['join']} $where $group $having $order";

        if (!is_null($params['limit']) || !is_null($params['offset'])) {
            $sql = self::query($type, 'limit', $sql, $params['limit'], $params['offset']);
        }

        return $sql;
    }

    /**
     * Permite construir el WHERE, GROUP BY, HAVING y ORDER BY de una cosnulta SQL
     * en base a los parametros $param.
     *
     * @param array $params
     */
    protected static function prepareParam(array $params)
    {
        return [
            static::where($params['where']),
            static::group($params['group']),
            static::having($params['having']),
            static::order($params['order']),
        ];
    }

    /**
     * Genera una sentencia where.
     *
     * @return string
     */
    protected static function where($where)
    {
        return empty($where)  ? '' : "WHERE $where";
    }

    /**
     * Genera una sentencia GROUP.
     *
     * @return string
     */
    protected static function group($group)
    {
        return empty($group)  ? '' : "GROUP BY $group";
    }

    /**
     * Genera una sentencia HAVING.
     *
     * @return string
     */
    protected static function having($having)
    {
        return empty($having)  ? '' : "HAVING $having";
    }

    /**
     * Genera una sentencia ORDER BY.
     *
     * @return string
     */
    protected static function order($order)
    {
        return empty($order)  ? '' : "ORDER BY $order";
    }

    /**
     * Construye una consulta INSERT.
     *
     * @param \Kumbia\ActiveRecord\LiteRecord $model Modelo a actualizar
     * @param array                           $data  Datos pasados a la consulta preparada
     *
     * @return string
     */
    public static function insert(\Kumbia\ActiveRecord\LiteRecord $model, &$data)
    {
        $meta = $model::metadata();
        $data = [];
        $columns = [];
        $values = [];

        // Preparar consulta
        foreach ($meta->getFieldsList() as $field) {
            $columns[] = $field;
            static::insertField($field, $model, $data, $values);
        }
        $columns = \implode(',', $columns);
        $values = \implode(',', $values);
        $source = $model::getSource();

        return "INSERT INTO $source ($columns) VALUES ($values)";
    }

    /**
     * Agrega un campo a para generar una consulta preparada para un INSERT.
     *
     * @param string     $field  Nombre del campo
     * @param LiteRecord $model  valor del campo
     * @param array      $data   array de datos
     * @param array      $values array de valores
     *
     * @return void
     */
    protected static function insertField($field, LiteRecord $model, array &$data, array &$values)
    {
        $meta = $model::metadata();
        $withDefault = $meta->getWithDefault();
        $autoFields = $meta->getAutoFields();
        if (self::haveValue($model, $field)) {
            $data[":$field"] = $model->$field;
            $values[] = ":$field";
        } else{//if (!\in_array($field, $withDefault) && !\in_array($field, $autoFields)) {
            $values[] = 'NULL';
        }
    }

    /**
     * Permite conocer si la columna debe definirse como nula.
     *
     * @param LiteRecord $model
     * @param string     $field
     *
     * @return bool
     */
    protected static function haveValue(LiteRecord $model, $field)
    {
        return isset($model->$field) && $model->$field !== '';
    }

    /**
     * Construye una consulta UPDATE.
     *
     * @param \Kumbia\ActiveRecord\LiteRecord $model Modelo a actualizar
     * @param array                           $data  Datos pasados a la consulta preparada
     *
     * @return string
     */
    public static function update(\Kumbia\ActiveRecord\LiteRecord $model, array &$data)
    {
        $set = [];
        $pk = $model::getPK();
        /*elimina la clave primaria*/
        $list = array_diff($model::metadata()->getFieldsList(), [$pk]);
        foreach ($list as $field) {
            $value = isset($model->$field) ? $model->$field : null;
            static::updateField($field, $value, $data, $set);
        }
        $set = \implode(', ', $set);
        $source = $model::getSource();
        $data[":$pk"] = $model->$pk;

        return "UPDATE $source SET $set WHERE $pk = :$pk";
    }

    /**
     * Generate SQL for DELETE sentence.
     *
     * @param string $source source
     * @param string $where  condition
     *
     * @return string SQL
     */
    public static function deleteAll($source, $where)
    {
        return "DELETE FROM $source ".static::where($where);
    }

    /**
     * Generate SQL for COUNT sentence.
     *
     * @param string $source source
     * @param string $where  condition
     *
     * @return string SQL
     */
    public static function count($source, $where)
    {
        return "SELECT COUNT(*) AS count FROM $source ".static::where($where);
    }

    /**
     * Agrega un campo a para generar una consulta preparada para un UPDATE.
     *
     * @param string $field Nombre del campo
     * @param mixed  $value valor
     * @param array  $data  array de datos
     * @param array  $set   array de valores
     *
     * @return void
     */
    protected static function updateField($field, $value, array &$data, array &$set)
    {
        if (!empty($value)) {
            $data[":$field"] = $value;
            $set[] = "$field = :$field";
        } else {
            $set[] = "$field = NULL";
        }
    }

    /**
     * Construye una consulta UPDATE.
     *
     * @param string      $model  nombre del modelo a actualizar
     * @param array       $fields campos a actualizar
     * @param array       $data   Datos pasados a la consulta preparada
     * @param string|null $where
     *
     * @todo ¿Hay que escapar los nombres de los campos?
     *
     * @return string
     */
    public static function updateAll($model, array $fields, array &$data, $where)
    {
        $set = [];
        //$pk = $model::getPK();
        /*elimina la clave primaria*/
        foreach ($fields as $field => $value) {
            static::updateField($field, $value, $data, $set);
        }
        $set = \implode(', ', $set);
        $source = $model::getSource();
        $where = static::where($where);

        return "UPDATE $source SET $set $where";
    }

    /**
     * Ejecuta una consulta.
     *
     * @param string $type           tipo de driver
     * @param string $query_function nombre de funcion
     *
     * @return mixed
     * @thow KumbiaException
     */
    public static function query($type, $query_function)
    {
        $query_function = "{$type}_{$query_function}";

        require_once __DIR__."/Query/{$query_function}.php";

        $args = \array_slice(\func_get_args(), 2);

        return call_user_func_array(__NAMESPACE__."\\Query\\$query_function", $args);
    }
}
