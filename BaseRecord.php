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
 * @copyright  2005 - 2016 Kumbia Team (http://www.kumbiaphp.com)
 * @license    http://wiki.kumbiaphp.com/Licencia     New BSD License
 */
namespace Kumbia\ActiveRecord;

use \PDO;
use \PDOStatement;
use \PDOException;
use \KumbiaException;

/**
 * Base del ORM ActiveRecord.
 */
abstract class BaseRecord
{
    
    public const VERSION = '0.5.3';

    /**
     * Database por defecto usa default.
     *
     * @var string
     */
    protected static $database = 'default';

    /**
     * Nombre de la tabla.
     *
     * @var string
     */
    protected static $table = '';

    /**
     * PK por defecto, si no existe mira en metadata.
     *
     * @var string
     */
    protected static $pk = 'id';

    /**
     * Constructor.
     *
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        $this->dump($data);
    }

    /**
     * Get the Primary Key value for the object
     * @return string
     */
    public function pk(): string
    {
        return $this->{static::$pk};
    }

    /**
     * Cargar datos al objeto.
     *
     * @param array $data
     */
    public function dump(array $data = []): void
    {
        foreach ($data as $k => $v) {
            $this->$k = $v;
        }
    }

    /**
     * Listado de los campos.
     *
     * @return string[]
     */
    public function getFields(): array
    {
        return \array_keys(\get_object_vars($this));
    }

    /**
     * Alias de los campos.
     *
     * @return string[]
     */
    public function getAlias(): array
    {
        //$humanize = function ()
        return \array_map('\ucwords', $this->getFields());
    }

    /**
     * Verifica que PK este seteado.
     *
     * @return bool
     */
    protected function hasPK(): bool
    {
        return isset($this->{static::$pk});
    }

    /**
     * Get the name of the primary key
     *
     * @return string
     */
    public static function getPK(): string
    {
        return static::$pk ?? static::$pk = self::metadata()->getPK();
    }

    /**
     * Obtiene nombre de tabla en la bd.
     *
     * @return string smallcase del nombre de la clase
     */
    public static function getTable(): string
    {
        if (static::$table) {
            return static::$table;
        }
        
        $split = \explode('\\', \get_called_class());
        $table = \preg_replace('/[A-Z]/', '_$0', \lcfirst(\end($split)));

        return static::$table = \strtolower($table);
    }

    /**
     * Obtiene el schema al que pertenece.
     *
     * @return string
     */
    public static function getSchema(): string
    {
        return '';
    }

    /**
     * Obtiene la combinación de esquema y tabla.
     *
     * @return string
     */
    public static function getSource(): string
    {
        $source = static::getTable();
        if ($schema = static::getSchema()) {
            $source = "$schema.$source";
        }

        return $source;
    }

    /**
     * Obtiene la conexión que se utilizará (contenidas en databases.php).
     *
     * @return string
     */
    public static function getDatabase(): string
    {
        return static::$database;
    }

    /**
     * Obtiene metadatos.
     *
     * @return Metadata\Metadata
     */
    public static function metadata(): Metadata\Metadata
    {
        return Metadata\Metadata::get(
            static::getDatabase(),
            static::getTable(),
            static::getSchema()
        );
    }

    /**
     * Obtiene manejador de conexion a la base de datos.
     *
     * @return \PDO
     */
    protected static function dbh(): \PDO
    {
        return Db::get(static::getDatabase());
    }

    /**
     * Consulta sql preparada.
     *
     * @param  string          $sql
     * 
     * @throws \PDOException
     * @return \PDOStatement
     */
    public static function prepare(string $sql): PDOStatement
    {
        $sth = self::dbh()->prepare($sql);
        $sth->setFetchMode(\PDO::FETCH_CLASS, static::class);

        return $sth;
    }

    /**
     * Retorna el último ID insertado.
     *
     * @return string
     */
    public static function lastInsertId(): string
    {
        return self::dbh()->lastInsertId();
    }

    /**
     * Consulta sql.
     *
     * @param  string          $sql
     * 
     * @throws \PDOException
     * @return \PDOStatement
     */
    public static function sql(string $sql): PDOStatement
    {
        return self::dbh()->query($sql, \PDO::FETCH_CLASS, static::class);
    }

    /**
     * Ejecuta consulta sql.
     *
     * @param  string        $sql
     * @param  array         $values valores
     * 
     * @throws PDOException 
     * @return bool|PDOStatement
     */
    public static function query(string $sql, array $values = [])
    {
        if (empty($values)) {
            return self::sql($sql);
        }

        $sth = self::prepare($sql);

        return $sth->execute($values) ? $sth : \false;
    }

    /**
     * Verifica si existe el registro.
     *
     * @param  string $pk valor para clave primaria
     * @return bool
     */
    public static function exists($pk): bool
    {
        $source  = static::getSource();
        $pkField = static::getPK();

        return self::query("SELECT COUNT(*) AS count FROM $source WHERE $pkField = ?", [$pk])->fetch()->count > 0;
    }

    /**
     * Paginar consulta sql.
     *
     * @param  string      $sql     consulta select sql
     * @param  int         $page    numero de pagina
     * @param  int         $perPage cantidad de items por pagina
     * @param  array       $values  valores
     * @return Paginator
     */
    public static function paginateQuery(string $sql, int $page, int $perPage, array $values = []): Paginator
    {
        return new Paginator(static::class, $sql, $page, $perPage, $values);
    }

    /**
     * Devuelve el nombre del drive PDO utilizado.
     *
     * @return string
     */
    public static function getDriver(): string
    {
        return self::dbh()->getAttribute(\PDO::ATTR_DRIVER_NAME);
    }

    /**
     * Comienza una trasacción.
     *
     * @throws PDOException If there is already a transaction started or the driver does not support transactions
     * @return bool
     */
    public static function begin(): bool
    {
        return self::dbh()->beginTransaction();
    }

    /**
     * Da marcha atrás a una trasacción.
     *
     * @throws PDOException if there is no active transaction.
     * @return bool
     */
    public static function rollback(): bool
    {
        return self::dbh()->rollBack();
    }

    /**
     * Realiza el commit de  una trasacción.
     *
     * @throws \PDOException if there is no active transaction.
     * @return bool
     */
    public static function commit(): bool
    {
        return self::dbh()->commit();
    }
}
