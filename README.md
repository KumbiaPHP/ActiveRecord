![KumbiaPHP](https://proto.kumbiaphp.com/img/kumbiaphp.svg)

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/KumbiaPHP/ActiveRecord/badges/quality-score.png?s=f7230602070a9e9605d46544197bcdac46166612)](https://scrutinizer-ci.com/g/KumbiaPHP/ActiveRecord/)
[![Code Coverage](https://scrutinizer-ci.com/g/KumbiaPHP/ActiveRecord/badges/coverage.png?s=58997633701e84050c0ebd5334f3eb1bb8b7ad42)](https://scrutinizer-ci.com/g/KumbiaPHP/ActiveRecord/)
[![Build Status](https://travis-ci.org/KumbiaPHP/ActiveRecord.png?branch=master)](https://travis-ci.org/KumbiaPHP/ActiveRecord)
[![Code Climate](https://codeclimate.com/github/KumbiaPHP/ActiveRecord/badges/gpa.svg)](https://codeclimate.com/github/KumbiaPHP/ActiveRecord)

ESPAÑOL - [ENGLISH](/README.en.md)

# ActiveRecord

Nuevo ActiveRecord en desarrollo.

No usar en producción

## Instalar con composer en KumbiaPHP

Necesita KumbiaPHP > 0.9RC

* Crear el archivo ***composer.json*** en la raiz del proyecto:

```yml
--proyecto  
    |  
    |--vendor  
    |--default  
    |--core  
    |--composer.json        Acá va nuestro archivo  
```

* Añadir el siguiente código:

```json
{
    "require": {
        "kumbia/activerecord" : "dev-master"
    }
}
```

* Ejecutar el comando **composer install**

* Seguir los pasos 2 y 3 de la siguiente sección.

## Instalar en KumbiaPHP

Necesita KumbiaPHP > 0.9RC

1. Copiar [config/config_databases.php](config/config_databases.php) en app/config/databases.php y configurar

2. (Opcional) Añadir en app/libs/ : [lite_record.php](#literecord) y/o [act_record.php](#actrecord)


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

//use Kumbia\ActiveRecord\LiteRecord as ORM;

class LiteRecord extends \Kumbia\ActiveRecord\LiteRecord
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

//use Kumbia\ActiveRecord\ActiveRecord;

class ActRecord extends \Kumbia\ActiveRecord\ActiveRecord
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
O directamente sin clase padre
```php
<?php
//app/models/personas.php

class Personas extends \Kumbia\ActiveRecord\LiteRecord
{

}
```
## Controller

```php
<?php
//app/controller/personas_controller.php

class PersonasController extends AppController {

    public function index() {
        $this->data = Personas::all();
    }
    
    public function find($id) {
        $this->data = Personas::get($id);
    }
}
```
###Uso de métodos en LiteRecord

####Filtrar datos

```php
    //obtener todos los registros como array
    $filas = Personas::all();
    echo $filas[0]->nombre;

    //obtener un registro por su clave primaria
    $fila = Personas::get($personaId);
    echo $fila->nombre;

    //obtener los registros como array según el filtro 
    $filas = Personas::filter("WHERE nombre LIKE ?", [$nombrePersona]);
    echo $filas[0]->nombre;

    //obtener registro según sql
    $fila = Personas::first("SELECT * FROM personas WHERE nombre = :nombre", [":nombre" => $nombrePersona]);
    echo $fila->nombre;

    //obtener array de registros según sql
    $filas = Personas::all("SELECT * FROM personas WHERE fecha_contrato >= ?", [$fechaContrato]);
    echo $filas[0]->nombre;
```

####DML / Crear, actualizar, borrar
```php
    //creando un nuevo registro
    $personaObj = new Personas();
    $personaObj->create([
        'nombre' => 'Edgard Baptista',
        'cargo' => 'Contador',
        'fecha_contrato' => date('Y-m-d'),
        'activo' => 1
    ]); //retorna True o False si hay éxito o error respectivamente

    //creando un nuevo registro //alternativa
    $personaObj = new Personas();
    $personaObj->save([
        'nombre' => 'Edgard Baptista',
        'cargo' => 'Contador',
        'fecha_contrato' => date('Y-m-d'),
        'activo' => 1
    ]); //retorna True o False si hay éxito o error respectivamente


    //actualizar un registro
    //primero buscar el registro que se quiere actualizar
    $personaObj = Personas::get($personaId);

    $personaObj->update([
        'nombre' => 'Edgard Baptista',
        'activo' => 0
    ]); //retorna True o False si hay éxito o error respectivamente

    //actualizar un registro //alternativa
    //primero buscar el registro que se quiere actualizar
    $personaObj = Personas::get($personaId);

    $personaObj->save([
        'nombre' => 'Edgard Baptista',
        'activo' => 0
    ]); //retorna True o False si hay éxito o error respectivamente


    //borrar un registro usando su clave primaria
    Personas::delete($personaId);
    
```
