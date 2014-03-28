![KumbiaPHP](http://proto.kumbiaphp.com/img/kumbiaphp.png)

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/KumbiaPHP/ActiveRecord/badges/quality-score.png?s=f7230602070a9e9605d46544197bcdac46166612)](https://scrutinizer-ci.com/g/KumbiaPHP/ActiveRecord/)
[![Code Coverage](https://scrutinizer-ci.com/g/KumbiaPHP/ActiveRecord/badges/coverage.png?s=58997633701e84050c0ebd5334f3eb1bb8b7ad42)](https://scrutinizer-ci.com/g/KumbiaPHP/ActiveRecord/)
[![Build Status](https://travis-ci.org/KumbiaPHP/ActiveRecord.png?branch=master)](https://travis-ci.org/KumbiaPHP/ActiveRecord)

ESPAÑOL - [ENGLISH](/README.en.md) - [DEV](/README-DEV.md)

# ActiveRecord

Nuevo ActiveRecord en desarrollo, requiere PHP 5.3

No usar en producción

## Instalar en KumbiaPHP

Necesita KumbiaPHP > 0.9RC

1. Copiar [config_databases.php](/config_databases.php) en app/config/databases.php y configurar
2. Copiar la carpeta lib/Kumbia en vendor.  (vendor/Kumbia/ActiveRecord/..)
3. Añadir en app/libs/ : [lite_record.php](#LiteRecord) y/o [act_record.php](#ActRecord)


### LiteRecord

Para los que prefieren SQL y las ventajas de un ORM, incluye un mini ActiveRecord

```php
<?php
//app/libs/lite_record.php

/**
 * Record 
 * Para los que prefieren SQL con las ventajas de ORM
 *
 * Clase padre para añadir tus métodos
 *
 * @category Kumbia
 * @package ActiveRecord
 * @subpackage LiteRecord
 */

use Kumbia\ActiveRecord\LiteRecord as ORM;

class LiteRecord extends ORM
{

}
```

### ActRecord


ActiveRecord completo

```php
<?php
//app/libs/act_record.php

/**
 * ActiveRecord Nuevo
 *
 * Clase padre para añadir tus métodos
 *
 * @category Kumbia
 * @package ActiveRecord
 * @subpackage ActiveRecord
 */

use Kumbia\ActiveRecord\ActiveRecord;

class ActRecord extends ActiveRecord
{

}
```

# Ejemplo

## Modelo

```php
<?php
//app/models/personas.php

class Personas extends ActRecord //o LiteRecord según el que prefiera
{

}
```

## Controller

```php
<?php
//app/controller/personas_controller.php

Load::models('personas');

class PersonasController extends AppController {

    public function index() {
        $this->data = Personas::all();
    }

}
```
