<?php
/**
 * The Prepare Query builder Class.
 *
 * @author RN Kushwaha <rn.kushwaha022@gmail.com>
 * @since v0.0.8 <Date: 29th Dec, 2019>
 */

namespace Dolphin\Builders;

use Dolphin\Connections\Connection;
use Dolphin\Parsers\WhereQueryParser;
use Dolphin\Utils\Utils;
use \Exception;

/**
 * This class provides the mechanism to build the Queries.
 */
class PrepareQueryBuilder extends QueryBuilder
{
    private function checkCountable($results = null ): bool{
       return (is_array($results) || is_object($results)) && count($results);
    }

    /**
     * It prepared and execute queries
     *
     * @param array $rows
     * @return boolean
     * @throws Exception
     * @author RN Kushwaha <rn.kushwaha022@gmail.com>
     * @since v0.0.5
     */
     public function prepare($where, $className, $query, $fetchRows = 'all')
     {
         $wqp  = new WhereQueryParser();
         $util = new Utils();
         $rows = null;

         try {
             $ar = $wqp->parseWhereQuery($where);
             $stmt = Connection::get()->prepare($this->queryPrefix($query));
             $stmt->execute($ar);

             if ($fetchRows == 'first') {
                 $results = $stmt->fetch(\PDO::FETCH_ASSOC);
             } else{
                 $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);
             }

             if($this->checkCountable($results) ){
               // now turn this stdClass object to the object type of calling model
               $rows = $util->turnObjects($className, $results);
             }

             return $rows;
         } catch (\PDOException $ex) {
             throw new \PDOException($ex->getMessage(), 1);
         } catch (Exception $e) {
             throw new Exception($e->getMessage(), 1);
         }
     }
}
