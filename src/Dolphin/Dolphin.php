<?php
/**
 * The Query builder API.
 *
 * @author RN Kushwaha <rn.kushwaha022@gmail.com>
 * @since v0.0.1 <Date: 12th April, 2019>
 */

namespace Dolphin\Mapper;

use Dolphin\Connections\Connection;
use Dolphin\Builders\QueryBuilder;
use Dolphin\Builders\WhereQueryBuilder;
use Dolphin\Builders\InsertQueryBuilder;
use Dolphin\Builders\UpdateQueryBuilder;
use Dolphin\Builders\DeleteQueryBuilder;
use Dolphin\Builders\PrepareQueryBuilder;
use \Exception;

/**
 * This class provides some nice features to interact with the Database
 * Elegant Query builder
 * Method Chaining
 * Prepared Statement using named parameter like status = :status
 * Raw Query Option
 * Join Clause
 * Where Clause
 * WhereRaw Clause
 * orWhere Clause [TODO]
 * WhereIn Clause
 * WhereNotIn Clause
 * WhereNull Clause
 * WhereNotNull Clause
 * GroupBy Clause
 * Having Clause
 * OrderBy Clause.
 *
 * Aggregations like
 * Count()
 * Max() [TODO]
 * Min() [TODO]
 * First()
 * Last() [TODO]
 * Avg() [TODO]
 * fetchColumn [TODO]
 * union() [TODO]
 * delete()
 * update()
 * insert()
 * truncate()
 * havingRaw() [TODO]
 * exists() [TODO]
 */
class Dolphin
{
    protected $fields = [];
    public $table;
    public $className;
    protected $groupBy;
    protected $orderBy;
    protected $having;
    protected $join = [];
    protected $leftJoin = [];
    protected $rightJoin = [];
    protected $crossJoin = [];
    protected $where = [];
    protected $whereRaw = [];
    protected $whereIn = [];
    protected $whereNotIn = [];
    protected $whereNull = [];
    protected $whereNotNull = [];
    protected $limit;
    protected $offset;
    protected $results;

    private function getFields(array $args, bool $quote = true){
        return (new QueryBuilder())->getFields($args, $quote);
    }

    private function validateArgsCount($noOfArgs){
        if($noOfArgs<2 || $noOfArgs >3){
            throw new Exception('Where parameter contains invalid number of parameters', 1);
        }
    }

    public function select()
    {
        $args = func_get_args();
        $fldAr = $this->getFields($args, true);
        $this->fields = array_merge($this->fields, $fldAr);

        return $this;
    }

    public function selectRaw()
    {
        $args = func_get_args();
        $fldAr = $this->getFields($args, false);
        $this->fields = array_merge($this->fields, $fldAr);

        return $this;
    }

    public function join($join, $mixedParam, $param3 = null, $param4 = null, $mixedParam2 = null)
    {
        $this->join = array_merge($this->join, [[$join, $mixedParam, $param3, $param4, $mixedParam2]]);

        return $this;
    }

    public function leftJoin($leftJoin, $mixedParam, $param3 = null, $param4 = null, $mixedParam2 = null)
    {
        $this->leftJoin = array_merge($this->leftJoin, [[$leftJoin, $mixedParam, $param3, $param4, $mixedParam2]]);

        return $this;
    }

    public function rightJoin($rightJoin, $mixedParam, $param3 = null, $param4 = null, $mixedParam2 = null)
    {
        $this->rightJoin = array_merge($this->rightJoin, [[$rightJoin, $mixedParam, $param3, $param4, $mixedParam2]]);

        return $this;
    }

    public function crossJoin($crossJoin, $params = null)
    {
        $this->crossJoin = array_merge($this->crossJoin, [[$crossJoin, $params]]);

        return $this;
    }

    /**
     * @throws Exception
     */
    public function where()
    {
        $args = func_get_args();
        $noOfArgs = func_num_args();
        $this->validateArgsCount($noOfArgs);

        if($noOfArgs===2){
            $this->where = array_merge($this->where, [[$args[0], '=', $args[1]]]);
            return $this;
        }

        $this->where = array_merge($this->where, [[$args[0], $args[1], $args[2]]]);
        return $this;
    }

    public function whereIn($whereIn, $params = [])
    {
        $this->whereIn = array_merge($this->whereIn, [[$whereIn, $params]]);

        return $this;
    }

    public function whereNotIn($whereNotIn, $params = [])
    {
        $this->whereNotIn = array_merge($this->whereNotIn, [[$whereNotIn, $params]]);

        return $this;
    }

    public function whereNull($whereNull)
    {
        $this->whereNull = array_merge($this->whereNull, [$whereNull]);

        return $this;
    }

    public function whereNotNull($whereNotNull)
    {
        $this->whereNotNull = array_merge($this->whereNotNull, [$whereNotNull]);

        return $this;
    }

    public function whereRaw($whereConditions)
    {
        $this->whereRaw = array_merge($this->whereRaw, [$whereConditions]);

        return $this;
    }

    public function offset($offset)
    {
        $this->offset = $offset;

        return $this;
    }

    public function limit($limit)
    {
        $this->limit = $limit;

        return $this;
    }

    public function orderBy($orderBy)
    {
        $this->orderBy = $orderBy;

        return $this;
    }

    public function groupBy($groupBy)
    {
        $this->groupBy = $groupBy;

        return $this;
    }

    public function having($having)
    {
        $this->having = $having;

        return $this;
    }

    /**
     * Builds Query added by method chaining.
     * It has the main logic of ORM
     */
    protected function buildQuery()
    {
        $query  = (new QueryBuilder())->buildQuery([
            'table' => $this->table,
            'fields' => $this->fields,
            'join' => $this->join,
            'leftJoin' => $this->leftJoin,
            'rightJoin' => $this->rightJoin,
            'crossJoin' => $this->crossJoin,
            'where' => $this->where,
            'whereRaw' => $this->whereRaw,
            'whereIn' => $this->whereIn,
            'whereNotIn' => $this->whereNotIn,
            'whereNull' => $this->whereNull,
            'whereNotNull' => $this->whereNotNull,
            'groupBy' => $this->groupBy,
            'having' => $this->having,
            'orderBy' => $this->orderBy,
            'limit' => $this->limit,
            'offset' => $this->offset
        ]);

        return join(' ', $query);
    }

    protected function reset()
    {
        $this->fields = [];
        $this->table = null;
        $this->className = null;
        $this->groupBy = null;
        $this->orderBy = null;
        $this->having = null;
        $this->join = [];
        $this->leftJoin = [];
        $this->rightJoin = [];
        $this->crossJoin = [];
        $this->where = [];
        $this->whereRaw = [];
        $this->whereIn = [];
        $this->whereNotIn = [];
        $this->whereNull = [];
        $this->whereNotNull = [];
        $this->limit = null;
        $this->offset = null;
    }

    public function prepare($query, $fetchRows = 'all')
    {
        $results = (new PrepareQueryBuilder())->prepare($this->where, $this->className, $query, $fetchRows);

        $this->results = $results;
        $this->reset();

        return $results;
    }

    public function query($query, $fetchRows = 'all')
    {
        $result = (new QueryBuilder())->query($query, $fetchRows);
        $this->reset();

        return $result;
    }

    public function get()
    {
        return $this->prepare($this->buildQuery());
    }

    public function first()
    {
        $query = $this->buildQuery();

        if (!strripos($query, 'LIMIT 1')) {
            $query .= ' LIMIT 1';
        }

        $result = $this->prepare($query, 'first');

        return isset($result->data) ? $result->data : '';
    }

    public function all()
    {
        $query = $this->buildQuery();
        $result = $this->prepare($query);

        return isset($result->data) ? $result->data : '';
    }

    /**
     * It fetches the row by primary key
     *
     * @since v0.0.5
     */
    public function find($id)
    {
        $this->where('id', $id);

        return $this->first();
    }

    /**
     * It fetches the row by primary key
     *
     * @param int $id
     * @return object $row
     * @throws Exception
     * @since v0.0.5
     */
    public function findOrFail($id)
    {
        $this->where('id', $id);
        $row = $this->first();

        if($row == null ){
            throw new Exception("The record does not exists!");
        }

        return $row;
    }

    public function count()
    {
        $this->fields = null;
        $query = $this->buildQuery();
        $query = str_replace('SELECT * ', 'SELECT COUNT(*) as count ', $query);

        return $this->query($query, 'count');
    }

    /**
     * It inserts the new rows
     *
     * @param array $rows
     * @return integer $lastInsertedId
     * @throws Exception
     * @since v0.0.5
     */
    public function insert($rows)
    {
        return (new InsertQueryBuilder())->insert($rows, $this->table);
    }

    /**
     * It updates the rows
     *
     * @param array $row
     * @return boolean
     * @throws Exception
     * @since v0.0.5
     */
    public function update($row)
    {
        $result =  (new UpdateQueryBuilder())->update(
          $row,
          $this->table,
          $this->where,
          $this->whereRaw,
          $this->whereIn,
          $this->whereNotIn,
          $this->whereNull,
          $this->whereNotNull
        );

        $this->reset();

        return $result;
    }

    /**
     * It truncates the table
     *
     * @return boolean
     * @throws Exception
     * @since v0.0.5
     */
    public function truncate()
    {
        $qb = new QueryBuilder();
        $query = "TRUNCATE ".$this->table;

        try{
            Connection::get()->query($qb->queryPrefix($query));
        } catch(Exception $e){
            throw new Exception($e->getMessage());
        }

        return true;
    }

    /**
     * It deleted the rows matched by where clause
     *
     * @return boolean
     * @throws Exception
     * @since v0.0.5
     */
    public function delete()
    {
        $result =  (new DeleteQueryBuilder())->delete(
          $this->table,
          $this->where,
          $this->whereRaw,
          $this->whereIn,
          $this->whereNotIn,
          $this->whereNull,
          $this->whereNotNull
        );

        $this->reset();

        return $result;
    }

}
