<?php
/**
 * The Query builder API.
 *
 * @author RN Kushwaha <rn.kushwaha022@gmail.com>
 *
 * @since v0.0.1 <Date: 12th April, 2019>
 */

namespace Dolphin\ORM;

use Dolphin\ORM\Builders\QueryBuilder;
use Dolphin\ORM\Connections\Connection;
use Dolphin\ORM\Utils\Utils;
use Exception;

class Save extends Dolphin
{
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
        $qb = new QueryBuilder();
        $util = new Utils();
        $row = $util->turnObject($this->tableName, $object);
        $ar = array();

        // our object is set. Now we need to save it
        if(isset($row) && isset($row->id) && $row->id > 0 ){
            $query = "UPDATE ".$this->table()." SET ";
            foreach($row as $key => $val){
                $ar[':'.$key] = $val;
                if($key == 'id') continue;
                $query.= $qb->quote($key)." =:".$key.",";
            }

            $query = rtrim($query, ",");
            $query.= " WHERE ".$qb->quote('id')."=:id";
        } else{
            $query = "INSERT INTO ".$this->table()." (";
            foreach($row as $key => $val){
                $query.= $qb->quote($key).", ";
            }

            $query = rtrim($query, ", ");
            $query.= ") VALUES (";
            foreach($row as $key => $val){
                $ar[$key] = $val;
                $query.= ":".$key.", ";
            }
            $query = rtrim($query, ", ");
            $query.=") ";
        }

        try{
            $stmt = Connection::get()->prepare($qb->queryPrefix($query));
            $stmt->execute($ar);
        } catch(Exception $e){
            throw new Exception($e->getMessage());
        }

        return true;
    }
    
}

