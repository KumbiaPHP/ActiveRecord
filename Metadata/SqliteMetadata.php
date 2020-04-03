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
 * Adaptador de Metadata para SQLite.
 */
class SqliteMetadata extends Metadata
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
        $describe = $pdo->query("PRAGMA table_info($table)", \PDO::FETCH_OBJ);
        //var_dump($results); die();
        $fields = [];
        foreach ($describe as $value) {
            $fields[$value->name] = [
                'Type' => \strtolower(\str_replace(' ', '', $value->type)),
                'Null' => $value->notnull == 0,
                'Default' => (bool) $value->dflt_value,
                'Key' => $value->pk == 1 ? 'PRI' : '',
                'Auto' => (\strtolower($value->type) == 'int' && $value->pk == 1) // using rowid
            ];
            $this->filterColumn($fields[$value->name], $value->name);
        }

        return $fields;
    }
}
