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
 * Adaptador de Metadata para Pgsql.
 */
class PgsqlMetadata extends Metadata
{
    /**
     * Consultar los campos de la tabla en la base de datos.
     *
     * @param  \PDO    $pdo      base de datos
     * @param  string  $table    tabla
     * @param  string  $schema   esquema, por defecto 'public'
     * 
     * @return array
     */
    protected function queryFields(\PDO $pdo, string $table, string $schema = ''): array
    {
        $schema = $schema === '' ? 'public' : $schema; // default to public
        // Nota: Se excluyen claves compuestas
        $describe = $pdo->query(
            "SELECT
                c.column_name AS field,
                c.udt_name AS type,
                tc.constraint_type AS key,
                c.column_default AS default,
                c.is_nullable AS null
            FROM information_schema.columns c
            LEFT OUTER JOIN information_schema.key_column_usage cu ON (
                cu.column_name = c.column_name AND cu.table_name = c.table_name AND (
                    SELECT COUNT(*) FROM information_schema.key_column_usage
                    WHERE constraint_name = cu.constraint_name
                ) = 1)
            LEFT OUTER JOIN information_schema.table_constraints tc
            ON (cu.constraint_name = tc.constraint_name AND tc.constraint_type
            IN ('PRIMARY KEY', 'UNIQUE'))
            WHERE c.table_name = '$table' AND c.table_schema = '$schema'
            ;",
            
            \PDO::FETCH_OBJ
        );

        return self::describe($describe->fetchAll());
    }

    /**
     * Genera la metadata.
     *
     * @param  array $describe
     * 
     * @return array
     */
    private function describe(array $describe): array
    {
        $fields = [];
        // TODO mejorar este código
        foreach ($describe as $value) {
            $fields[$value->field] = [
                'Type'    => $value->type,
                'Null'    => $value->null !== 'NO',
                'Default' => $value->default != '',
                'Key'     => \substr($value->key, 0, 3),
                'Auto'    => (bool) \preg_match('/^nextval\(/', $value->default)
            ];
            $this->filterColumn($fields[$value->field], $value->field);
        }

        return $fields;
    }
}
