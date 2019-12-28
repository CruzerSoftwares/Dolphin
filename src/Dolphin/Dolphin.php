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
use Dolphin\Parsers\WhereQueryParser;
use Dolphin\Utils\Utils;
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
    protected $fields = array();
    public $table;
    public $className;
    protected $groupBy;
    protected $orderBy;
    protected $having;
    protected $join = array();
    protected $leftJoin = array();
    protected $rightJoin = array();
    protected $crossJoin = array();
    protected $where = array();
    protected $whereRaw = array();
    protected $whereIn = array();
    protected $whereNotIn = array();
    protected $whereNull = array();
    protected $whereNotNull = array();
    protected $limit;
    protected $offset;
    protected $results;

    private function getFields(array $args, bool $quote = true){
        $fldAr = array();
        $qb = new QueryBuilder();

        foreach ($args as $arg) {
            foreach (explode(',', $arg) as $ar) {
                $fldAr[] = ($quote === true) ? $qb->quote(trim($ar)) : trim($ar);
            }
        }

        return $fldAr;
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

    public function whereIn($whereIn, $params = array())
    {
        $this->whereIn = array_merge($this->whereIn, [[$whereIn, $params]]);

        return $this;
    }

    public function whereNotIn($whereNotIn, $params = array())
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
        $qb     = new QueryBuilder();

        $query  = $qb->buildQuery([
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
        $this->fields = array();
        $this->table = null;
        $this->className = null;
        $this->groupBy = null;
        $this->orderBy = null;
        $this->having = null;
        $this->join = array();
        $this->leftJoin = array();
        $this->rightJoin = array();
        $this->crossJoin = array();
        $this->where = array();
        $this->whereRaw = array();
        $this->whereIn = array();
        $this->whereNotIn = array();
        $this->whereNull = array();
        $this->whereNotNull = array();
        $this->limit = null;
        $this->offset = null;
    }

    public function prepare($query, $fetchRows = 'all')
    {
        $qb   = new QueryBuilder();
        $wqp  = new WhereQueryParser();
        $util = new Utils();
        $rows = null;

        try {
            $ar = $wqp->parseWhereQuery($this->where);
            $stmt = Connection::get()->prepare($qb->queryPrefix($query));
            $stmt->execute($ar);

            if ($fetchRows == 'first') {
                $this->results = $stmt->fetch(\PDO::FETCH_OBJ);
            } else{
                $this->results = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            }

            if(count($this->results) ){
              // now turn this stdClass object to the object type of calling model
              $rows = $util->turnObjects($this->className, $this->results);
            }
            // Reset class variables
            $this->reset();

            return $rows;
        } catch (\PDOException $ex) {
            throw new \PDOException($ex->getMessage(), 1);
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), 1);
        }
    }

    public function query($query, $fetchRows = 'all')
    {
        $qb = new QueryBuilder();

        try {
            $obj = Connection::get()->query($qb->queryPrefix($query), \PDO::FETCH_OBJ);

            if ($fetchRows == 'count') {
                $obj = $obj->fetchColumn();
            }

            // Reset class variables
            $this->reset();

            return $obj;
        } catch (\PDOException $ex) {
            throw new \PDOException($ex->getMessage(), 1);
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), 1);
        }
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

        return $this->prepare($query, 'first');
    }

    public function all()
    {
        $query = $this->buildQuery();

        return $this->prepare($query);
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
