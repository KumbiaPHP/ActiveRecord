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
 * @copyright  Copyright (c) 2005-2013 Kumbia Team (http://www.kumbiaphp.com)
 * @license    http://wiki.kumbiaphp.com/Licencia     New BSD License
 */

namespace Kumbia\ActiveRecord\Metadata;

use Kumbia\ActiveRecord\Db;
use PDO;

/**
 * Adaptador de Metadata para Mysql
 *
 */
class MysqlMetadata extends Metadata
{

    /**
     * Consultar los campos de la tabla en la base de datos
     *
     * @param  string $database base de datos
     * @param  string $table    tabla
     * @param  string $schema   squema
     * @return array
     */
    protected function queryFields($database, $table, $schema = null)
    {
        if (!$schema) {
            $describe = Db::get($database)->query("DESCRIBE `$table`");
        } else {
            $describe = Db::get($database)->query("DESCRIBE `$schema`.`$table`");
        }

        $fields = array();
        while(( $value = $describe->fetch(PDO::FETCH_OBJ))) {
            $fields[$value->Field] = array(
                'Type' => $value->Type,
                'Null' => $value->Null != 'NO',
                'Key' => $value->Key,
                'Default' => $value->Default != '',
                'Auto' => $value->Extra == 'auto_increment'
            );
        }
        return $fields;
    }

}
