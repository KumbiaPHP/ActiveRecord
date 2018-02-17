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
 * @subpackage Query
 * @copyright  Copyright (c) 2005-2016  Kumbia Team (http://www.kumbiaphp.com)
 * @license    http://wiki.kumbiaphp.com/Licencia     New BSD License
 */

namespace Kumbia\ActiveRecord\Query;

/**
 * Adiciona limit y offset a la consulta sql en mssql
 *
 * @param string $sql consulta select
 * @param string $limit valor limit
 * @param string $offset valor offset
 * @return string
 */
function sqlsrv_limit($sql, $limit = null, $offset = null)
{
    if ($limit !== null) {
        $limit = (int) $limit;
        $sql = preg_replace('/(DELETE|INSERT|SELECT|UPDATE)/i', '${1} TOP ' . $limit, $sql);
    }

    if ($offset !== null) {
        $offset = (int) $offset;
        $sql .= " OFFSET $offset";
    }

    return $sql;
}
