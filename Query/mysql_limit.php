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

namespace Kumbia\ActiveRecord\Query;

/**
 * Adiciona limit y offset a la consulta sql en mysql.
 *
 * @param string $sql    consulta select
 * @param string $limit  valor limit
 * @param string $offset valor offset
 *
 * @return string
 */
function mysql_limit($sql, int $limit, int $offset = 0)
{
    $sql .= " LIMIT $offset, $limit";

    return $sql;
}
