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
 * Generador de codigo SQL
 */
class QueryGenerator
{
    
    /**
     * Construye una consulta select desde una lista de parametros
     *
     * @param  array  $params parametros de consulta select
     *                        where: condiciones where
     *                        order: criterio de ordenamiento
     *                        fields: lista de campos
     *                        join: joins de tablas
     *                        group: agrupar campos
     *                        having: condiciones de grupo
     *                        limit: valor limit
     *                        offset: valor offset
     * @param string $source
     * @param string $type
     * @return string
     */
    public static function select($source, $type, Array $params)
    {
        $params = array_merge(array(
            'fields' => '*',
            'join'   => '',
            'limit'  => null,
            'offset' => null,
            'where'  => null,
            'group'  => null,
            'having' => null,
            'order'  => null,
        ), $params);

        list($where, $group, $having, $order) = static::prepareParam($params);
        $sql = "SELECT {$params['fields']} FROM $source {$params['join']} $where $group $having $order";

        if (!is_null($params['limit']) || !is_null($params['offset'])) {
            $sql = Query\query_exec($type, 'limit', $sql, $params['limit'], $params['offset']);
        }
        return $sql;
    }
    
     /**
      * Permite construir el WHERE, GROUP BY, HAVING y ORDER BY de una cosnulta SQL
      * en base a los parametros $param
      * @param Array  $params
      */
    protected static function prepareParam(Array $params){
        return array(
            static::where($params['where']),
            static::group($params['group']),
            static::having($params['having']),
            static::order($params['order']),
        );
    }

    /**
     * Genera una sentencia where
     * @return string 
     */
    protected static function where($where){
        return empty($where)  ? '': "WHERE $where";
    }

    /**
     * Genera una sentencia GROUP
     * @return string 
     */
    protected static function group($group){
        return empty($group)  ? '': "GROUP BY $group";
    }

    /**
     * Genera una sentencia HAVING
     * @return string 
     */
    protected static function having($having){
        return empty($having)  ? '': "HAVING $having";
    }

     /**
     * Genera una sentencia ORDER BY
     * @return string 
     */
    protected static function order($order){
        return empty($order)  ? '': "ORDER BY $order";
    }

     /**
     * Construye una consulta INSERT
     * @param \Kumbia\ActiveRecord\LiteRecord $model Modelo a actualizar
     * @param Array $data Datos pasados a la consulta preparada
     * @return string
     */
    public static function insert(\Kumbia\ActiveRecord\LiteRecord $model, &$data){
        $meta = $model::metadata();
        $data = array();
        $columns = array();
        $values = array();
       
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
     * Agrega un campo a para generar una consulta preparada para un INSERT
     * @param string $field Nombre del campo
     * @param LiveRecord  $model valor del campo
     * @param Array  $data array de datos
     * @param Array  $values array de valores
     * @return void
     */
    protected static function insertField($field, LiteRecord $model, Array &$data, Array &$values){
        $meta = $model::metadata();
        $withDefault =  $meta->getWithDefault();
        $autoFields =   $meta->getAutoFields();
        if (!empty($model->$field)) {
            $data[":$field"] = $model->$field;
            $values[] = ":$field";
        } elseif (!\in_array($field, $withDefault) && !\in_array($field, $autoFields)) {
            $values[] = 'NULL';
        }
    }

    /**
     * Construye una consulta UPDATE 
     * @param \Kumbia\ActiveRecord\LiteRecord $model Modelo a actualizar
     * @param Array $data Datos pasados a la consulta preparada
     * @return string
     */
    public static function update(\Kumbia\ActiveRecord\LiteRecord $model, &$data){
        $data = array();
        $set = array();
        $pk = $model::getPK();
        // Preparar consulta
        foreach ($model::metadata()->getFieldsList() as $field) {
            if (!empty($model->$field)) {
                $data[":$field"] = $model->$field;
                if($field != $pk) $set[] = "$field = :$field";
            } else {
                $set[] = "$field = NULL";
            }
        }
        $set = \implode(', ', $set);

        $source = $model::getSource();

        return "UPDATE $source SET $set WHERE $pk = :$pk";
    }


}
