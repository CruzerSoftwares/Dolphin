<?php
/**
 * The Query builder API.
 *
 * @author RN Kushwaha <rn.kushwaha022@gmail.com>
 * @since v0.0.1 <Date: 16th April, 2019>
 */

namespace Dolphin\Builders;

use Dolphin\Connections\Connection;
use \Exception;

/**
 * This class provides the mechanism to build the Queries.
 */
class QueryBuilder
{
    protected $whereAdded = false;

    public function queryPrefix($query)
    {
        return str_replace('#__', Connection::getPrefix(), $query);
    }

    public function getPrefix()
    {
        return Connection::getPrefix();
    }

    public function fetchType($fetchMode = 'FETCH_OBJ')
    {
        switch ($fetchMode) {
            case 'FETCH_ASSOC': $fetch = \PDO::FETCH_ASSOC; break;
            case 'FETCH_NUM': $fetch = \PDO::FETCH_NUM; break;
            case 'FETCH_BOTH': $fetch = \PDO::FETCH_BOTH; break;
            case 'FETCH_BOUND': $fetch = \PDO::FETCH_BOUND; break;
            case 'FETCH_CLASS': $fetch = \PDO::FETCH_CLASS; break;
            default: $fetch = \PDO::FETCH_OBJ;
        }

        return $fetch;
    }

    public function addAlias($tableName)
    {
        $tableAlias = '';

        if (stripos($tableName, ' as ') > 0) {
            $tblName = explode(' as ', $tableName);
            $tableAlias = ' AS '.$this->quote($tblName[1]);
            $tableName = $tblName[0];
        }

        return [$tableName, $tableAlias];
    }

    public function quote($field)
    {
        if (strpos($field, '.') !== false) {
            $field = str_replace('.', '`.`', $field);
        }

        return '`'.$field.'`';
    }

    public function enclose($field)
    {
        return "'".$field."'";
    }

    private function getQueryFields($fields, $tbl){
        $startQuery = join(', ', $fields);
        if (empty($fields)) {
            $startQuery = $this->quote($tbl).'.*';
        }

        return $startQuery;
    }

    private function buildLimitQuery($limit, $offset, $query = []){
      $limitQuery = [];
      if (!empty($limit)) {
          $query[] = 'LIMIT';

          if (!empty($offset)) {
              $query[] = $offset.',';
          }

          $query[] = $limit;
      }

      if (count($limitQuery)) {
          $query = array_merge($query, $limitQuery);
      }
      return $query;
    }

    public function query($query, $fetchRows){
      try {
          $obj = Connection::get()->query($this->queryPrefix($query), \PDO::FETCH_OBJ);

          if ($fetchRows == 'count') {
              $obj = $obj->fetchColumn();
          }

          return $obj;
      } catch (\PDOException $ex) {
          throw new \PDOException($ex->getMessage(), 1);
      } catch (Exception $e) {
          throw new Exception($e->getMessage(), 1);
      }
    }

    public function buildQuery(array $params)
    {
        $jqb    = new JoinQueryBuilder();
        $wqb    = new WhereQueryBuilder();

        $prefix = $this->getPrefix();
        $tblWithPrefix = $params['table'];
        $tbl    = str_replace($prefix, '', $tblWithPrefix);
        $query  = [];

        $query[] = 'SELECT';
        $query[] = $this->getQueryFields($params['fields'], $tbl);
        $query[] = 'FROM';
        $query[] = $this->quote($tblWithPrefix).' AS '.$this->quote($tbl);

        $query = $jqb->buildAllJoinQuery(
                                $params['join'],
                                $params['leftJoin'],
                                $params['rightJoin'],
                                $params['crossJoin'],
                                $query
                            );

        $query = $wqb->buildAllWhereQuery(
                                    $params['where'],
                                    $params['whereRaw'],
                                    $params['whereIn'],
                                    $params['whereNotIn'],
                                    $params['whereNull'],
                                    $params['whereNotNull'],
                                    $query
                                );

        if (!empty($params['groupBy'])) {
            $query[] = 'GROUP BY';
            $query[] = $params['groupBy'];
        }

        if (!empty($params['having'])) {
            $query[] = 'HAVING';
            $query[] = $params['having'];
        }

        if (!empty($params['orderBy'])) {
            $query[] = 'ORDER BY';
            $query[] = $params['orderBy'];
        }

        $query = $this->buildLimitQuery($params['limit'], $params['offset'], $query);

        return $query;
    }

    public function getFields(array $args, bool $quote = true): array{
        $fldAr = [];

        foreach ($args as $arg) {
            foreach (explode(',', $arg) as $ar) {
                $fldAr[] = ($quote === true) ? $this->quote(trim($ar)) : trim($ar);
            }
        }

        return $fldAr;
    }
}
