Cambios (tachado lo que ya está listo)
=======

Clase Metadata
--------------
~~Para manejar toda la metadata, su cache, info de bd, alias,...~~

~~Tendrá adapters para las diferentes bd (aunque tendriamos que probar getColumnMeta()~~

Clase Validate
--------------
Clase externa para validar cualquier modelo, aunque no extienda de ActiveRecord

Clase DB
--------------
En el config: 

~~enviar directo el dsn, y no crearlo en la clase (ya que usa siempre PDO)~~

~~Mejor mirar el tipo de base de datos una vez conectado usando ATTR_DRIVER_NAME~~

~~Posibilidad de pasar via config, parámetros opcionales a la conexión PDO~~

~~usar php para app/config/databases.php~~

Base de datos por defecto
-------------------
~~Por defecto, usará default (no es necesario mirar el config.ini)~~

Es el mismo trabajo, cambiar el config.ini o el databases.ini

Para cambiarla directamente en app/libs/ActRecord.php o en el modelo, en getDatabase().

Varios
------
Para sanitizar usará el quote() de PDO. Que no es necesario al usar consultas preparadas

Para saber el tipo de base de datos usará: $name = $conn->getAttribute(PDO::ATTR_DRIVER_NAME);

~~Query listo para consultas preparadas  de un golpe (posible opción de especificar el fetch)~~

~~$this->query($sql, $data)~~

~~No usar las constantes APP_PATH ni CORE PATH para desacoplar la lib~~

Clases: LiteRecord (básico sólo SQL), ActRecord (con generador de consultas, extenderá SqlRecord) y ActiveRecord (compatible con el actual, extenderá SqlRecord)

~~No usar la clase Util, para desacoplar mejor.~~

En producción usará cache de metadata indefinido, posible uso de APC si esta disponible, sino nixfile

Añadir método para limpiar la cache de metadata

Metadata info de la bd, version, driver, ....

Métodos firstByXxxx() y allByXxxx() en LiteRecord

Posibilidad de añadir prefijo al getTable()