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
 * @subpackage Metadata
 * @copyright  2005 - 2016  Kumbia Team (http://www.kumbiaphp.com)
 * @license    http://wiki.kumbiaphp.com/Licencia     New BSD License
 */
namespace Kumbia\ActiveRecord\Metadata;

use Kumbia\ActiveRecord\Db;
use PDO;

/**
 * Adaptador de Metadata para Sqlsrv
 *
 */
class SqlsrvMetadata extends Metadata
{
    /**
     * Consultar los campos de la tabla en la base de datos
     *
     * @param string $database base de datos
     * @param string $table    tabla
     * @param string $schema   squema
     *
     * @return array
     */
    protected function queryFields($database, $table, $schema='dbo')
    {
        $sql = "SELECT
            c.name AS field_name,
            c.is_identity AS is_auto_increment,
            c.is_nullable,
            object_definition(c.default_object_id) AS default_value,
            t.name AS type_field
        FROM sys.columns c join sys.types t 
        ON c.system_type_id = t.user_type_id
        WHERE object_id = object_id('$schema.$table')";
        $describe = Db::get($database)->query($sql);
        $fields = array();
        $pk = Db::get($database)->query("exec sp_pkeys @table_name='$table'");
        $pk = $pk->fetch(PDO::FETCH_OBJ);
        $pk = $pk->COLUMN_NAME;
        while( ( $value = $describe->fetch(PDO::FETCH_OBJ) ) ) :
            $fields[$value->field_name] = array(
                'Type' => $value->type_field,
                'Null' => $value->is_nullable ? 1 : '',
                'Key' => ($value->field_name == $pk) ? 'PRI' : '',
                'Default' => str_replace("''", "'", trim($value->default_value, "(')") ),
                'Auto' => ($value->is_auto_increment) ? 'auto_increment' : ''
            );
            $this->filterCol($fields[$value->field_name], $value->field_name);
        endwhile;
        return $fields;
    }
}
