<?php
/**
 * The Update Query builder Class.
 *
 * @author RN Kushwaha <rn.kushwaha022@gmail.com>
 * @since v0.0.8 <Date: 29th Dec, 2019>
 */

namespace Dolphin\Builders;

use Dolphin\Connections\Connection;
use \Exception;

/**
 * This class provides the mechanism to build the update Queries.
 */
class UpdateQueryBuilder extends QueryBuilder
{
    /**
     * It updates the matching rows
     *
     * @param array $rows
     * @return boolean
     * @throws Exception
     * @author RN Kushwaha <rn.kushwaha022@gmail.com>
     * @since v0.0.8
     */
    public function update(
      $row,
      $table,
      $where,
      $whereRaw,
      $whereIn,
      $whereNotIn,
      $whereNull,
      $whereNotNull
    ): bool
    {
        $wqb   = new WhereQueryBuilder();
        $query = "UPDATE ".$table." SET ";
        $ar    = [];

        foreach($row as $key => $val){
            $ar[':'.$key] = $val;
            $query.= $this->quote($key)." =:".$key.",";
        }

        $query = rtrim($query, ",");

        try{
            $whereQuery = $wqb->buildAllWhereQuery(
                                $where,
                                $whereRaw,
                                $whereIn,
                                $whereNotIn,
                                $whereNull,
                                $whereNotNull
                            );
            $query.= " ".join(" ", $whereQuery);
            $stmt = Connection::get()->prepare($this->queryPrefix($query));
            $stmt->execute($ar);
        } catch(Exception $e){
            throw new Exception($e->getMessage());
        }

        return true;
    }
}
