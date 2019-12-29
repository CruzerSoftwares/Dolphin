<?php
/**
 * The Delete Query builder Class.
 *
 * @author RN Kushwaha <rn.kushwaha022@gmail.com>
 * @since v0.0.8 <Date: 29th Dec, 2019>
 */

namespace Dolphin\Builders;

use Dolphin\Connections\Connection;
use Exception;

/**
 * This class provides the mechanism to build the Delete Queries.
 */
class DeleteQueryBuilder extends QueryBuilder
{
    /**
     * It deletes the matching rows
     *
     * @param array $rows
     * @return boolean
     * @throws Exception
     * @author RN Kushwaha <rn.kushwaha022@gmail.com>
     * @since v0.0.8
     */
    public function delete(
      $table,
      $where,
      $whereRaw,
      $whereIn,
      $whereNotIn,
      $whereNull,
      $whereNotNull
    ): bool
    {
        $wqb = new WhereQueryBuilder();
        $query = "DELETE FROM ".$table;

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
            Connection::get()->query($this->queryPrefix($query));
        } catch(Exception $e){
            throw new Exception($e->getMessage());
        }

        return true;
    }
}
