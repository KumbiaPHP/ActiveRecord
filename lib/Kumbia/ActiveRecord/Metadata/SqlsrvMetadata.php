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
    protected function queryFields($database, $table, $schema = null)
    {
        $sql = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='$table'";
        $describe = Db::get($database)->query($sql);
        $fields = array();
        $pk = Db::get($database)->query("exec sp_pkeys @table_name='$table'");
        $pk = $pk->fetch(PDO::FETCH_OBJ);
        $pk = $pk->COLUMN_NAME;
        // TODO mejorar este código, la consulta SQL no usa el $schema
        while (( $value = $describe->fetch(PDO::FETCH_OBJ))) :
            $fields[$value->COLUMN_NAME] = array(
                'Type' => $value->DATA_TYPE,
                'Null' => $value->IS_NULLABLE,
                'Key' => ($value->COLUMN_NAME == $pk) ? 'PRI' : '',
                'Default' => $value->COLUMN_DEFAULT,
                'Auto' => ''
            );
            $this->filterCol($fields[$value->COLUMN_NAME], $value->COLUMN_NAME);
        endwhile;
        return $fields;
    }
}
