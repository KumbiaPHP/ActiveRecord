ActiveRecord
============

Nuevo ActiveRecord, requiere PHP 5.3


Cambios
=======

Clase Metadata
--------------
Para manejar toda la metadata, su cache, info de bd, alias,... 

Tendrá adapters para las diferentes bd (aunque tendriamos que probar getColumnMeta()


Base de datos por defecto
-------------------
Por defecto, usará default (no es necesario mirar el config.ini)

Es el mismo trabajo, cambiar el config.ini o el databases.ini

Otra forma será cambiarla directamente en app/libs/ActRecord.php. Tanto el atributo o dinámicamente en el initialize().

Varios
------
Para sanitizar usará el quote() de PDO. Que no es necesario al usar consultas preparadas

Para saber el tipo de base de datos usará: $name = $conn->getAttribute(PDO::ATTR_DRIVER_NAME);

Query listo para consultas preparadas  de un golpe (posible opción de especificar el fetch)

$this->query($sql, $data)

Clases: SqlRecord (básico sólo SQL), ActRecord (con generador de consultas, extenderá SqlRecord) y ActiveRecord (compatible con el actual, extenderá SqlRecord)

No usar la clase Util, para desacoplar mejor.


