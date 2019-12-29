<?php
/**
 * The Query builder API.
 *
 * @author RN Kushwaha <rn.kushwaha022@gmail.com>
 * @since v0.0.1 <Date: 16th April, 2019>
 */

namespace Dolphin\Builders;

use Dolphin\Parsers\WhereQueryParser;

/**
 * This class provides the mechanism to build the Where Queries.
 */
class WhereQueryBuilder extends QueryBuilder
{
    private $qb;

    public function __construct(){
        $this->qb = new QueryBuilder();
    }

    private function buildWhereInClauseQuery($terms = [])
    {
        if (is_int($terms[0])) {
            $dataStr = join(',', $terms);
            return $dataStr;
        } elseif (is_string($terms[0])) {
            $dataStr = join("', '", $terms);
            $dataStr = "'".$dataStr."'";
            return $dataStr;
        }

        return null;
    }

    private function whereAddedCondition($conditions = []){
      return $this->whereAdded === false && count($conditions);
    }

    public function buildWhereQuery($conditions = [])
    {
        $whereQuery = [];
        if (count($conditions)<=0) {
            return [];
        }

        $firstTime = true;
        if ($this->whereAddedCondition($conditions)) {
          $whereQuery[] = 'WHERE';
          $this->whereAdded = true;
        }

        foreach ($conditions as $where) {
            $sign = '=';
            $whereQueryPart = 'AND ';
            if(count($where)==3) {
                $sign = $where[1];
            }

            if ($firstTime) {
                $whereQueryPart = '';
                $firstTime = false;
            }
            $whereQuery[] = $whereQueryPart.$this->qb->quote(trim($where[0])).' '.$sign.' '.$this->qb->enclose(end($where));
        }

        return $whereQuery;
    }

    public function buildWhereRawQuery($conditions = [], $query = [])
    {
        $whereRawQuery = [];

        if (count($conditions)<=0) {
            return $query;
        }

        $firstTime = true;
        if ($this->whereAddedCondition($conditions)) {
            $whereRawQuery[] = 'WHERE';
            $this->whereAdded = true;
        }

        foreach ($conditions as $whereRaw) {
            $whereRawQueryPart = 'AND ';
            if ($firstTime === true) {
                $whereRawQueryPart = '';
                $firstTime = false;
            }
            $whereRawQuery[] = $whereRawQueryPart.$whereRaw;
        }

        if (count($whereRawQuery)) {
            $query = array_merge($query, $whereRawQuery);
        }
        return $query;
    }

    private function buildWhereInNotInQuery($conditions = [], $queryParam='IN')
    {
        $query = [];

        if (count($conditions)<=0) {
            return $query;
        }

        $firstTime = false;
        if ($this->whereAdded === false) {
            $query[] = 'WHERE';
            $firstTime = true;
            $this->whereAdded = true;
        }

        foreach ($conditions as $whereIn) {
            $dataStr = $this->buildWhereInClauseQuery($whereIn[1]);
            $queryPart = 'AND ';
            if ($dataStr === null) {
                continue;
            }

            if ($firstTime) {
                $queryPart = '';
                $firstTime = false;
            }

            $query[] = $queryPart.trim($whereIn[0]).' '.$queryParam.' ('.$dataStr.')';
        }

        return $query;
    }

    public function buildWhereInQuery($conditions = [])
    {
        return $this->buildWhereInNotInQuery($conditions, 'IN');
    }

    public function buildWhereNotInQuery($conditions = [])
    {
        return $this->buildWhereInNotInQuery($conditions, 'NOT IN');
    }

    private function buildWhereNullNotNullQuery($conditions = [], $query = [], $queryParam = 'IS NULL')
    {
        $whereQuery = [];

        if (count($conditions)<=0) {
            return $query;
        }

        $firstTime = false;
        if ($this->whereAdded === false) {
            $whereQuery[] = 'WHERE';
            $firstTime = true;
            $this->whereAdded = true;
        }

        foreach ($conditions as $whereNull) {
            $whereQueryPart = 'AND ';
            if ($firstTime) {
                $whereQueryPart = '';
                $firstTime = false;
            }

            $whereQuery[] = $whereQueryPart.trim($whereNull).' '.$queryParam;
        }
        if (count($whereQuery)) {
            $query = array_merge($query, $whereQuery);
        }
        return $query;
    }

    public function buildWhereNullQuery($conditions = [], $query = [])
    {
        return $this->buildWhereNullNotNullQuery($conditions, $query, 'IS NULL');
    }

    public function buildWhereNotNullQuery($conditions = [], $query = [])
    {
        return $this->buildWhereNullNotNullQuery($conditions, $query, 'IS NOT NULL');
    }

    public function buildAllWhereQuery($where, $whereRaw, $whereIn, $whereNotIn, $whereNull, $whereNotNull, $mainQuery = [])
    {
        $query = $this->buildWhereQuery($where);
        $query = $this->buildWhereRawQuery($whereRaw, $query);
        $whereInQuery = $this->buildWhereInQuery($whereIn);
        if (count($whereInQuery)) {
            $query = array_merge($query, $whereInQuery);
        }
        $whereNotInQuery = $this->buildWhereNotInQuery($whereNotIn);
        if (count($whereNotInQuery)) {
            $query = array_merge($query, $whereNotInQuery);
        }
        $query = $this->buildWhereNullQuery($whereNull, $query);
        $query = $this->buildWhereNotNullQuery($whereNotNull, $query);

        if (count($query)) {
            $mainQuery = array_merge($mainQuery, $query);
        }
        return $mainQuery;
    }
}
