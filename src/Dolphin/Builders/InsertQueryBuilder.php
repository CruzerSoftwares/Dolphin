<?php
/**
 * The Insert Query builder Class.
 *
 * @author RN Kushwaha <rn.kushwaha022@gmail.com>
 *
 * @since v0.0.5 <Date: 09th May, 2019>
 */

namespace Dolphin\Builders;

use Dolphin\Connections\Connection;
use Exception;

/**
 * This class provides the mechanism to build the Insert Queries.
 */
class InsertQueryBuilder extends QueryBuilder
{
    public function buildInsert($table, $obj){
        $qb = new QueryBuilder();
        $query = "INSERT INTO ".$table." (";
        foreach($obj as $key => $val){
            $query.= $qb->quote($key).", ";
        }

        $query = rtrim($query, ", ");
        $query.= ") VALUES ";

        return $query;
    }

    public function buildInsertPlaceholder($rows){
        $ar = array();
        $query = "(";

        foreach($rows as $key => $val){
            $ar[$key] = $val;
            $query.= ":".$key.", ";
        }

        $query = rtrim($query, ", ");
        $query.=") ";

        return ['query' => $query, 'array' => $ar];
    }

    public function buildInsertPlaceholders($rows){
        $bindAr = array();
        $query = "";

        foreach($rows as $i => $row){
            $query.="(";
            foreach($row as $key => $val){
                $param = ":" . $key . $i;
                $query.= $param.", ";
                $bindAr[$param] = $val;
            }

            $query = rtrim($query, ", ");
            $query.="), ";
        }

        $query = rtrim($query, ", ");
        return ['query' => $query, 'array' => $bindAr];
    }

    private function checkMultipleInsert($rows = array()){
      return is_array($rows) && isset($rows[0]) && is_array($rows[0]);
    }
    /**
     * It inserts the new rows
     *
     * @param array $rows
     * @return integer $lastInsertedId
     * @throws Exception
     * @author RN Kushwaha <rn.kushwaha022@gmail.com>
     * @since v0.0.5
     */
    public function insert($rows, $table)
    {
        $qb = new QueryBuilder();
        $db = Connection::get();

        $dataToBuild = $rows;
        $methodToCall = 'buildInsertPlaceholder';

        if($this->checkMultipleInsert($rows)){
            $dataToBuild = $rows[0];
            $methodToCall = 'buildInsertPlaceholders';
        }

        $query   = $this->buildInsert($table, $dataToBuild);
        $dataRet = $this->$methodToCall($rows);
        $query.= $dataRet['query'];
        $bindAr = $dataRet['array'];

        try{
            $stmt = $db->prepare($qb->queryPrefix($query));

            if($this->checkMultipleInsert($rows)){
                foreach($bindAr as $param => $val){
                    $stmt->bindValue($param, $val);
                }

                $stmt->execute();
                return $db->lastInsertId();
            }

            $stmt->execute($bindAr);
            return $db->lastInsertId();
        } catch(Exception $e){
            throw new Exception($e->getMessage());
        }
    }
}
