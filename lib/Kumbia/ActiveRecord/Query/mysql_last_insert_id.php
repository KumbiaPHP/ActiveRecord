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
 * Obtiene el último id generado en mysql.
 *
 * @param \PDO   $dbh    conexion pdo
 * @param string $pk     campo clave primaria
 * @param string $table  nombre de tabla
 * @param string $schema esquema
 *
 * @return string
 */
function mysql_last_insert_id(\PDO $dbh, $pk, $table, $schema = null)
{
    return $dbh->lastInsertId();
}
