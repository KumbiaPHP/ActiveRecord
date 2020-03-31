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

use \PDO;

/**
 * Adaptador de Metadata para Mysql.
 */
class MysqlMetadata extends Metadata
{
    /**
     * Consultar los campos de la tabla en la base de datos.
     *
     * @param  \PDO    $pdo      base de datos
     * @param  string  $table    tabla
     * @param  string  $schema   squema
     * 
     * @return array
     */
    protected function queryFields(\PDO $pdo, string $table, string $schema = ''): array
    {
        $sql      = $schema ? "DESCRIBE `$schema`.`$table`" : "DESCRIBE `$table`";
        $describe = $pdo->query($sql, \PDO::FETCH_OBJ);

        $fields = [];
        // TODO mejorar este código
        while ($value = $describe->fetch()) {
            $fields[$value->Field] = [
                'Type'    => $value->Type,
                'Null'    => $value->Null !== 'NO',
                'Key'     => $value->Key,
                'Default' => $value->Default != '',
                'Auto'    => $value->Extra === 'auto_increment'
            ];
            $this->filterColumn($fields[$value->Field], $value->Field);
        }

        return $fields;
    }
}
