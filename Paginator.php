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
 * @copyright  Copyright (c) 2005-2013 Kumbia Team (http://www.kumbiaphp.com)
 * @license    http://wiki.kumbiaphp.com/Licencia     New BSD License
 */
 
namespace ActiveRecord;
 
/**
 * Implementación de paginador
 * 
 */
class Paginator implements \Iterator
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
     * Número de items recorridos
     * 
     * @var int
     */
    private $_position = 0;
    
    /**
     * Cantidad de items ha recorrer
     * 
     * @var int
     */
    private $_rowCount = 0;
    
    /**
     * Constructor
     * 
     * @param string $model nombre de clase de modelo
     * @param string $sql consulta select sql
     * @param int $page numero de pagina
     * @param int $perPage cantidad de items por pagina
     * @param array $values valores
     */
    public function __construct($model, $sql, $page, $perPage, $values = null)
    {
        //Si la página o por página es menor de 1 (0 o negativo)
        if ($page < 1 || $perPage < 1) {
            throw new KumbiaException("La página $page no existe en el páginador");
        }
        $start = $perPage * ($page - 1);
        
        // Valores para consulta
        if($values !== null && !is_array($values)) $values = array_slice(func_get_args(), 4);
        
        //Cuento las apariciones atraves de una tabla derivada
        $n = $model::query("SELECT COUNT(*) AS count FROM ($sql) AS t", $values)->fetch()->count;
        
        //si el inicio es superior o igual al conteo de elementos,
        //entonces la página no existe, exceptuando cuando es la página 1
        if ($page > 1 && $start >= $n) throw new \KumbiaException("La página $page no existe en el páginador");
        
        // Establece el limit y offset
        require_once __DIR__ . '/Query/query_exec.php';
		$sql = Query\query_exec($model::getDatabase(), 'limit', $sql, $perPage, $start);
        
        $this->items = $model::query($sql, $values);
        $this->_rowCount = $this->items->rowCount();
        
        //Se efectuan los calculos para las páginas
        $this->next = ($start + $perPage) < $n ? ($page + 1) : null;
        $this->prev = ($page > 1) ? ($page - 1) : null;
        $this->current = $page;
        $this->total = ceil($n / $perPage);
        $this->count = $n;
        $this->per_page = $perPage;
	}
	
	public function rewind() 
	{}

	/**
	 * Obtiene el item actual
	 * 
	 * @return mixed
	 */
    public function current() 
    {
        return $this->items->fetch();
    }

	/**
	 * Obtiene key
	 * 
	 * @return int
	 */
    public function key() 
    {
        return $this->_position;
    }

	/**
	 * Avanza iterador
	 * 
	 * @return mixed
	 */
    public function next() 
    {
		$this->_position++;
    }

	/**
	 * Verifica si es valida la iteracion
	 * 
	 * @return boolean
	 */
    public function valid() 
    {
        return $this->_position < $this->_rowCount;
    }
}
