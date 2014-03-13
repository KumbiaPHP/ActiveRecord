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
    public static function select($source, $params)
    {
        $params = array_merge(array(
            'fields' => '*',
            'join'   => '',
            'limit'  => null,
            'offset' => null
        ), $params);

        $where  = isset($params['where'])  ? "WHERE {$params['where']}"   : '';
        $group  = isset($params['group'])  ? "GROUP BY {$params['group']}": '';
        $having = isset($params['having'])  ? "HAVING {$params['having']}" : '';
        $order  = isset($params['order'])  ? "ORDER BY {$params['order']}": '';

        $sql = "SELECT {$params['fields']} FROM $source {$params['join']} $where $group $having $order";

        if (!is_null($params['limit']) || !is_null($params['offset'])) {
            $type = self::dbh()->getAttribute(\PDO::ATTR_DRIVER_NAME);
            $sql = Query\query_exec($type, 'limit', $sql, $params['limit'], $params['offset']);
        }
        return $sql;
    }
  
}
