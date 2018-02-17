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
 * @copyright  2005 - 2016  Kumbia Team (http://www.kumbiaphp.com)
 * @license    http://wiki.kumbiaphp.com/Licencia     New BSD License
 */

namespace Kumbia\ActiveRecord;

/**
 * Implementación de paginador.
 */
class Paginator implements \IteratorAggregate, \Countable, \JsonSerializable
{
    /**
     * Número de página actual.
     *
     * @var int
     */
    protected $page;

    /**
     * Cantidad de items por página.
     *
     * @var int
     */
    protected $perPage;

    /**
     * Número de páginas totales.
     *
     * @var int
     */
    protected $totalPages;

    /**
     * Cantidad de items totales.
     *
     * @var int
     */
    protected $count;

    /**
     * Nombre del modelo a usar.
     *
     * @var string
     */
    protected $model;

    /**
     * Cadena SQL a ejecutar.
     *
     * @var string
     */
    protected $sql;

    /**
     * Párametros de la consulta.
     *
     * @var array|null
     */
    protected $values;

    /**
     * Items de pagina.
     *
     * @var array de objetos
     */
    private $items;

    /**
     * Constructor.
     *
     * @param string $model   nombre de clase de modelo
     * @param string $sql     consulta select sql
     * @param int    $page    numero de pagina
     * @param int    $perPage cantidad de items por pagina
     * @param mixed  $values  valores
     */
    public function __construct($model, $sql, int $page, int $perPage, $values = null)
    {
        $this->perPage = $perPage;
        $this->page = $page;

        /*validacion*/
        $this->validPage();

        $this->model = $model;

        // Valores para consulta
        $this->values = ($values !== null && !is_array($values)) ?
        array_slice(func_get_args(), 4) : $values;

        $this->count = $this->countQuery($model, $sql);
        $this->totalPages = (int) max(1, ceil($this->count / $this->perPage));
        $this->validCurrent();
        // Establece el limit y offset
        $this->sql = QueryGenerator::query($model::getDriver(), 'limit', $sql, $perPage, ($page - 1) * $perPage);
        $this->items = $model::query($this->sql, $this->values)->fetchAll();
    }

    /**
     * Permite que al usar json_encode() con una instacia de Paginator funcione correctamente
     * retornando los items del paginador.
     */
    public function jsonSerialize()
    {
        return $this->items;
    }

    /**
     * Verifica que la pagina sea válida.
     */
    private function validPage()
    {
        //Si la página o por página es menor de 1 (0 o negativo)
        if ($this->page < 1 || $this->perPage < 1) {
            throw new \RangeException("La página $this->page no existe", 404);
        }
    }

    /**
     * Valida que la página actual.
     */
    private function validCurrent()
    {
        if ($this->page > $this->totalPages) {
            throw new \RangeException("La página $this->page no existe", 404);
        }
    }

    /**
     * (non-PHPdoc).
     *
     * @see IteratorAggregate::getIterator()
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->items);
    }

    /**
     * Cuenta el número de resultados totales.
     *
     * @param string $model
     * @param string $sql
     *
     * @return int total de resultados
     */
    protected function countQuery($model, $sql)
    {
        $query = $model::query("SELECT COUNT(*) AS count FROM ($sql) AS t", $this->values)->fetch();

        return (int) $query->count;
    }

    /**
     * Total de items.
     *
     * @return int
     */
    public function totalItems()
    {
        return $this->count;
    }

    /**
     * Total de páginas.
     *
     * @return int
     */
    public function totalPages()
    {
        return $this->totalPages;
    }

    /**
     * Calcula el valor de la próxima página.
     *
     * @return int
     */
    public function nextPage()
    {
        return ($this->totalPages > $this->page) ? ($this->page + 1) : null;
    }

    /**
     * Calcula el valor de la página anterior.
     *
     * @return int
     */
    public function prevPage()
    {
        return ($this->page > 1) ? ($this->page - 1) : null;
    }

    /**
     * Items devueltos.
     *
     * @see Countable::countable()
     *
     * @return int
     */
    public function count()
    {
        return count($this->items);
    }

    /**
     * Página actual de paginador.
     *
     * @return int
     */
    public function page()
    {
        return $this->page;
    }

    /**
     * Campos del objeto.
     *
     * @return array
     */
    public function getFields()
    {
        return $this->items[0]->getFields();
    }

    /**
     * Alias de Campos del objeto.
     *
     * @return array
     */
    public function getAlias()
    {
        return $this->items[0]->getAlias();
    }
}
