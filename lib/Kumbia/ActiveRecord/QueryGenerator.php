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
     * @return string
     */
    public static function select($source, Array $params)
    {
        $params = array_merge(array(
            'fields' => '*',
            'join'   => '',
            'limit'  => null,
            'offset' => null
        ), $params);

        list($where, $group, $having, $order) = static::prepareParam($params);
        $sql = "SELECT {$params['fields']} FROM $source {$params['join']} $where $group $having $order";

        if (!is_null($params['limit']) || !is_null($params['offset'])) {
            $type = self::dbh()->getAttribute(\PDO::ATTR_DRIVER_NAME);
            $sql = Query\query_exec($type, 'limit', $sql, $params['limit'], $params['offset']);
        }
        return $sql;
    }
    
     /**
      * Permite construir el WHERE, GROUP BY, HAVING y ORDER BY de una cosnulta SQL
      * en base a los parametros $param
      * @param Array  $param 
      */
    protected static function prepareParam(Array $param){
        $where  = empty($params['where'])  ? '': "WHERE {$params['where']}"   ;
        $group  = empty($params['group'])  ? '': "GROUP BY {$params['group']}";
        $having = empty($params['having']) ? '': "HAVING {$params['having']}" ;
        $order  = empty($params['order'])  ? '': "ORDER BY {$params['order']}";
        return array($where, $group, $having, $order);
    }
}
