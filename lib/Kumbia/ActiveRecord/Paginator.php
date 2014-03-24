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
 * @copyright  Copyright (c) 2005-2014  Kumbia Team (http://www.kumbiaphp.com)
 * @license    http://wiki.kumbiaphp.com/Licencia     New BSD License
 */

namespace Kumbia\ActiveRecord;

/**
 * Implementación de paginador
 *
 */
class Paginator implements \IteratorAggregate
{
    /**
     * Items de pagina
     *
     * @var PDOStatement
     */
    public $items;

    /**
     * Numero de página siguiente
     *
     * @var int
     */
    public $next;

    /**
     * Número de página anterior
     *
     * @var int
     */
    public $prev;

    /**
     * Número de página actual
     *
     * @var int
     */
    public $current;

    /**
     * Número de páginas totales
     *
     * @var int
     */
    public $total;

    /**
     * Cantidad de items totales
     *
     * @var int
     */
    public $count;

    /**
     * Cantidad de items por página
     *
     * @var int
     *
     * TODO: colocar en camelcase
     */
    public $per_page;

    /**
     * Cantidad de items ha recorrer
     *
     * @var int
     */
    private $_rowCount = 0;

    /**
     * Nombre del modelo a usar
     * @var string
     */
    protected $_model;

    /**
     * Cadena SQL a ejecutar
     * @var String
     */
    protected $_sql;

    /**
     * Valores de los parametros de la consulta
     * @var array
     */
    protected $_values;


    /**
     * Constructor
     *
     * @param string $model   nombre de clase de modelo
     * @param string $sql     consulta select sql
     * @param int    $page    numero de pagina
     * @param int    $perPage cantidad de items por pagina
     * @param array  $values  valores
     */
    public function __construct($model, $sql, $page, $perPage, $values = null)
    {
        $this->per_page = $perPage;
        $this->current = $page;

        /*validacion*/
        $this->validPage();
        
        $this->_model = $model;
        $this->_sql = $sql;

        // Valores para consulta
        $this->_values = ($values !== null && !is_array($values)) ?
                    array_slice(func_get_args(), 4) : $values;
    }

    /**
     * Verifica que la pagina sea válida
     */
    protected function validPage(){
        //Si la página o por página es menor de 1 (0 o negativo)
        if ($this->current < 1 || $this->per_page < 1) {
            throw new KumbiaException("La página $this->current no existe en el páginador");
        }
    }

    /**
     * Valida que la pagina actual sea válida
     * @param int comienzo
     */
    protected function validCurrent($start){
        //si el inicio es superior o igual al conteo de elementos,
        //entonces la página no existe, exceptuando cuando es la página 1
        if ($this->current > 1 && $start >= $this->count)
            throw new \KumbiaException("La página $this->current no existe en el páginador");
    }


    /**
     * Implementa el retroceso de cursor en la iteración
     * @todo Mover este procedimiento a otro metodo y usar cursores iterables, ya se volveria a hacer la cnsulta
     * @return void
     */

    public function getIterator() 
    {
        $model = $this->_model;

        $start = $this->per_page * ($this->current - 1);

        $this->count = $this->countQuery();
        //valida
        $this->validCurrent($start);
        // Establece el limit y offset
        $this->_sql = Query\query_exec($model::getDriver(), 'limit', $this->_sql, $this->per_page, $start);
        $this->items = $model::query($this->_sql, $this->_values);
        $this->_rowCount = $this->items->rowCount();
        //Se efectuan los calculos para las páginas
        $this->next = $this->nextPage($start);
        $this->prev = $this->prevPage();
        $this->total = ceil($this->count / $this->per_page);
        return new \ArrayIterator($this->items->fetchAll());
    }

    /**
     * Cuenta el número de resultados totales
     * @return int total de resultados
     */
    protected function countQuery(){
        //Cuento las apariciones atraves de una tabla derivada
        $model = $this->_model;
        $query = $model::query("SELECT COUNT(*) AS count FROM ($this->_sql) AS t", $this->_values)->fetch();
        return (int)$query->count;
    }

    /**
     * Calcula el valor de la proxima página
     * @param int $start registro donde se comienza
     * @return int
     */
    protected function nextPage($start){
        return ($start + $this->per_page) < $this->count ? ($this->current + 1) : null;
    }
    /**
     * Calcula el valor de la página anterior
     * @return int
     */
    protected function prevPage(){
        return ($this->current > 1) ? ($this->current - 1) : null;
    }
}
