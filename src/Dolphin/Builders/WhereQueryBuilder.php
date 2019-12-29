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

    private function buildWhereInClauseQuery($terms = array())
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

    private function whereAddedCondition($conditions = array()){
      return $this->whereAdded === false && count($conditions);
    }

    public function buildWhereQuery($conditions = array())
    {
        $whereQuery = array();
        if (count($conditions)<=0) {
            return array();
        }

        $firstTime = true;
        if ($this->whereAddedCondition($conditions)) {
          $whereQuery[] = 'WHERE';
          $this->whereAdded = true;
        }

        foreach ($conditions as $where) {
            $sign = '=';
            if(count($where)==3) {
                $sign = $where[1];
            }
            if ($firstTime) {
                $whereQuery[] = $this->qb->quote(trim($where[0])).' '.$sign.' '.$this->qb->enclose(end($where));
                $firstTime = false;
            } else {
                $whereQuery[] = 'AND '.$this->qb->quote(trim($where[0])).' '.$sign.' '.$this->qb->enclose(end($where));
            }
        }

        return $whereQuery;
    }

    public function buildWhereRawQuery($conditions = array(), $query = array())
    {
        $whereRawQuery = array();

        if (count($conditions)<=0) {
            return $query;
        }

        $firstTime = true;
        if ($this->whereAddedCondition($conditions)) {
            $whereRawQuery[] = 'WHERE';
            $this->whereAdded = true;
        }

        foreach ($conditions as $whereRaw) {
            if ($firstTime === true) {
                $whereRawQuery[] = $whereRaw;
                $firstTime = false;
            } else {
                $whereRawQuery[] = 'AND '.$whereRaw;
            }
        }

        if (count($whereRawQuery)) {
            $query = array_merge($query, $whereRawQuery);
        }
        return $query;
    }

    public function buildWhereInQuery($conditions = array())
    {
        $whereInQuery = array();

        if (count($conditions)<=0) {
            return array();
        }

        $firstTime = false;
        if ($this->whereAdded === false) {
            $whereInQuery[] = 'WHERE';
            $firstTime = true;
            $this->whereAdded = true;
        }

        foreach ($conditions as $whereIn) {
            $dataStr = $this->buildWhereInClauseQuery($whereIn[1]);
            if ($dataStr === null) {
                continue;
            }

            if ($firstTime) {
                $whereInQuery[] = trim($whereIn[0]).' IN ('.$dataStr.')';
                $firstTime = false;
            } else {
                $whereInQuery[] = 'AND '.trim($whereIn[0]).' IN ('.$dataStr.')';
            }
        }

        return $whereInQuery;
    }

    public function buildWhereNotInQuery($conditions = array())
    {
        $whereNotInQuery = array();

        if (count($conditions)<=0) {
            return array();
        }

        $firstTime = false;
        if ($this->whereAdded === false) {
            $whereNotInQuery[] = 'WHERE';
            $firstTime = true;
            $this->whereAdded = true;
        }

        foreach ($conditions as $whereNotIn) {
            $dataStr = $this->buildWhereInClauseQuery($whereNotIn[1]);
            if ($dataStr === null) {
                continue;
            }

            if ($firstTime) {
                $whereNotInQuery[] = trim($whereNotIn[0]).' NOT IN ('.$dataStr.')';
                $firstTime = false;
            } else {
                $whereNotInQuery[] = 'AND '.trim($whereNotIn[0]).' NOT IN ('.$dataStr.')';
            }
        }

        return $whereNotInQuery;
    }

    public function buildWhereNullQuery($conditions = array(), $query = array())
    {
        $whereNullQuery = array();

        if (count($conditions)<=0) {
            return $query;
        }

        $firstTime = false;
        if ($this->whereAdded === false) {
            $whereNullQuery[] = 'WHERE';
            $firstTime = true;
            $this->whereAdded = true;
        }

        foreach ($conditions as $whereNull) {
            if ($firstTime) {
                $whereNullQuery[] = trim($whereNull).' IS NULL';
                $firstTime = false;
            } else {
                $whereNullQuery[] = 'AND '.trim($whereNull).' IS NULL';
            }
        }
        if (count($whereNullQuery)) {
            $query = array_merge($query, $whereNullQuery);
        }
        return $query;
    }

    public function buildWhereNotNullQuery($conditions = array(), $query = array())
    {
        $whereNotNullQuery = array();

        if (count($conditions)<=0) {
            return $query;
        }

        $firstTime = false;
        if ($this->whereAdded === false) {
            $whereNotNullQuery[] = 'WHERE';
            $firstTime = true;
            $this->whereAdded = true;
        }

        foreach ($conditions as $whereNotNull) {
            if ($firstTime) {
                $whereNotNullQuery[] = trim($whereNotNull).' IS NOT NULL';
                $firstTime = false;
            } else {
                $whereNotNullQuery[] = 'AND '.trim($whereNotNull).' IS NOT NULL';
            }
        }
        if (count($whereNotNullQuery)) {
            $query = array_merge($query, $whereNotNullQuery);
        }
        return $query;
    }

    public function buildAllWhereQuery($where, $whereRaw, $whereIn, $whereNotIn, $whereNull, $whereNotNull, $mainQuery = array())
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
