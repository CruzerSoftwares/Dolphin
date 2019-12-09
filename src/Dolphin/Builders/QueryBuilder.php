<?php
/**
 * The Query builder API.
 *
 * @author RN Kushwaha <rn.kushwaha022@gmail.com>
 *
 * @since v0.0.1 <Date: 16th April, 2019>
 */

namespace Dolphin\Builders;

use Dolphin\Connections\Connection;

/**
 * This class provides the mechanism to build the Queries.
 */
class QueryBuilder
{
    protected $whereAdded;

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

        if (strpos($tableName, ' AS ') > 0) {
            $tblName = explode(' AS ', $tableName);
            $tableAlias = ' AS '.self::quote($tblName[1]);
            $tableName = $tblName[0];
        }

        if (strpos($tableName, ' as ') > 0) {
            $tblName = explode(' as ', $tableName);
            $tableAlias = ' AS '.self::quote($tblName[1]);
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

    public function buildQuery(array $params)
    {
        $jqb    = new JoinQueryBuilder();
        $wqb    = new WhereQueryBuilder();

        $prefix = $this->getPrefix();
        $tblWithPrefix = $params['table'];
        $tbl    = str_replace($prefix, '', $tblWithPrefix);
        $query  = [];

        $query[] = 'SELECT';
        $startQuery = join(', ', $params['fields']);
        if (empty($params['fields'])) {
            $startQuery = $this->quote($tbl).'.*';
        }
        
        $query[] = $startQuery;
        $query[] = 'FROM';
        $query[] = $this->quote($tblWithPrefix).' AS '.$this->quote($tbl);

        $allJoinQuery = $jqb->buildAllJoinQuery(
                                $params['join'], 
                                $params['leftJoin'], 
                                $params['rightJoin'], 
                                $params['crossJoin']
                            );
        if (count($allJoinQuery)) {
            $query = array_merge($query, $allJoinQuery);
        }

        $allWhereQuery = $wqb->buildAllWhereQuery(
                                    $params['where'], 
                                    $params['whereIn'], 
                                    $params['whereNotIn'], 
                                    $params['whereNull'], 
                                    $params['whereNotNull']
                                );

        if (count($allWhereQuery)) {
            $query = array_merge($query, $allWhereQuery);
        }

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

        if (!empty($params['limit'])) {
            $query[] = 'LIMIT';

            if (!empty($params['offset'])) {
                $query[] = $params['offset'].',';
            }

            $query[] = $params['limit'];
        }

        return $query;
    }
}
