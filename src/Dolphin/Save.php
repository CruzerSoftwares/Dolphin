<?php
/**
 * The Query builder API.
 *
 * @author RN Kushwaha <rn.kushwaha022@gmail.com>
 * @since v0.0.1 <Date: 12th April, 2019>
 */

namespace Dolphin\Mapper;

use Dolphin\Builders\QueryBuilder;
use Dolphin\Connections\Connection;
use Dolphin\Utils\Utils;
use Exception;

class Save extends Dolphin
{
    private $qb;

    public function __construct()
    {
        $this->qb = new QueryBuilder();
    }

    public function buildQueryStrSingleFromArr($row = array()){
      $ar = [];
      $query = "UPDATE ".$this->table." SET ";
      foreach($row as $key => $val){
          $ar[':'.$key] = $val;
          if($key == 'id') continue;
          $query.= $this->qb->quote($key)." =:".$key.",";
      }
      $query = rtrim($query, ",");

      return ['ar' => $ar, 'query' => $query];
    }
    public function createQuery($row){
        $ar = [];
        if(isset($row) && isset($row->id) && $row->id > 0 ){
            $mixedData = $this->buildQueryStrSingleFromArr($row);
            $query= $mixedData['query']." WHERE ".$this->qb->quote('id')."=:id";

            return ['query' => $query, 'data' => $mixedData['ar']];
        }

        $queryVal = '';
        $query = "INSERT INTO ".$this->table." (";
        foreach($row as $key => $val){
            $query.= $this->qb->quote($key).", ";
            $ar[$key] = $val;
            $queryVal.= ":".$key.", ";
        }

        $query = rtrim($query, ", ").") VALUES (".$queryVal.rtrim($query, ", ").") ";

        return ['query' => $query, 'data' => $ar];
    }

    /**
     * It saves the row by primary key [id]
     * Or inserts a new row if id is null
     *
     * @param object $object
     * @return boolean
     * @throws Exception
     * @author RN Kushwaha <rn.kushwaha022@gmail.com>
     * @since v0.0.5
     */
    public function save($object)
    {
        $util = new Utils();
        $row = $util->turnObject($this->className, $object);

        list($query, $data) = $this->createQuery($row);

        try{
            $stmt = Connection::get()->prepare($this->qb->queryPrefix($query));
            $stmt->execute($data);
        } catch(Exception $e){
            throw new Exception($e->getMessage());
        }

        return true;
    }

}
