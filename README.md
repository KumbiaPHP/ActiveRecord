![KumbiaPHP](http://proto.kumbiaphp.com/img/kumbiaphp.png)

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/KumbiaPHP/ActiveRecord/badges/quality-score.png?s=f7230602070a9e9605d46544197bcdac46166612)](https://scrutinizer-ci.com/g/KumbiaPHP/ActiveRecord/)
[![Code Coverage](https://scrutinizer-ci.com/g/KumbiaPHP/ActiveRecord/badges/coverage.png?s=58997633701e84050c0ebd5334f3eb1bb8b7ad42)](https://scrutinizer-ci.com/g/KumbiaPHP/ActiveRecord/)
[![Build Status](https://travis-ci.org/KumbiaPHP/ActiveRecord.png?branch=master)](https://travis-ci.org/KumbiaPHP/ActiveRecord)

ESPAÑOL - [ENGLISH](/README.en.md) - [DEV](/README-DEV.md)

ActiveRecord
============

Nuevo ActiveRecord en desarrollo, requiere PHP 5.3

No usar en producción

Instalar en KumbiaPHP
--------------


1. Copiar [config_databases.php](/config_databases.php) en app/config/databases.php y configurar
2. Copiar la carpeta lib/Kumbia en vendor.  (vendor/Kumbia/ActiveRecord/..)
3. Añadir en app/libs/ : [lite_record.php](#LiteRecord) y/o [act_record.php](#ActRecord)


LiteRecord
----------

    <?php
    //app/libs/lite_record.php
    
    /**
     * Record 
     * Para los que prefieren SQL con las ventajas de ORM
     *
     * Clase padre para añadir tus métodos
     *
     * @category Kumbia
     * @package Db
     * @subpackage ActiveRecord
     */
    
    use Kumbia\ActiveRecord\LiteRecord as ORM;
    
    
    class LiteRecord extends ORM {
    
    }

ActRecord
---------

    <?php
    //app/libs/act_record.php
    
    /**
     * ActiveRecord Nuevo
     *
     * Clase padre para añadir tus métodos
     *
     * @category Kumbia
     * @package Db
     * @subpackage ActiveRecord
     */
    
    use Kumbia\ActiveRecord\ActiveRecord;
    
    
    class ActRecord extends ActiveRecord {
    
    }
