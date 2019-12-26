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

    public function buildWhereQuery($conditions = array())
    {
        $query = array();

        if (!count($conditions)) {
            return $query;
        }

        $firstTime = true;
        $query[] = 'WHERE';
        $this->whereAdded = true;

        foreach ($conditions as $where) {
            $sign = '=';
            if(count($where)==3) {
                $sign = $where[1];
            }
            if ($firstTime) {
                $query[] = $this->qb->quote(trim($where[0])).' '.$sign.' '.end($where);
                $firstTime = false;
            } else {
                $query[] = 'AND '.$this->qb->quote(trim($where[0])).' '.$sign.' '.end($where);
            }
        }

        return $query;
    }

    public function buildWhereInClauseQuery($terms = array())
    {
        if (is_int($terms[0])) {
            $dataStr = join(',', $terms);
        } elseif (is_string($terms[0])) {
            $dataStr = join("', '", $terms);
            $dataStr = "'".$dataStr."'";
        } else {
            return null;
        }

        return $dataStr;
    }

    public function buildWhereInQuery($conditions = array())
    {
        $query = array();

        if (!count($conditions)) {
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
            if ($dataStr === null) {
                continue;
            }

            if ($firstTime) {
                $query[] = trim($whereIn[0]).' IN ('.$dataStr.')';
                $firstTime = false;
            } else {
                $query[] = 'AND '.trim($whereIn[0]).' IN ('.$dataStr.')';
            }
        }

        return $query;
    }

    public function buildWhereNotInQuery($conditions = array())
    {
        $query = array();

        if (!count($conditions)) {
            return $query;
        }
            
        $firstTime = false;
        if ($this->whereAdded === false) {
            $query[] = 'WHERE';
            $firstTime = true;
            $this->whereAdded = true;
        }

        foreach ($conditions as $whereNotIn) {
            $dataStr = $this->buildWhereInClauseQuery($whereNotIn[1]);
            if ($dataStr === null) {
                continue;
            }

            if ($firstTime) {
                $query[] = trim($whereNotIn[0]).' NOT IN ('.$dataStr.')';
                $firstTime = false;
            } else {
                $query[] = 'AND '.trim($whereNotIn[0]).' NOT IN ('.$dataStr.')';
            }
        }

        return $query;
    }

    public function buildWhereNullQuery($conditions = array())
    {
        $query = array();

        if (!count($conditions)) {
            return $query;
        }
        
        $firstTime = false;
        if ($this->whereAdded === false) {
            $query[] = 'WHERE';
            $firstTime = true;
            $this->whereAdded = true;
        }

        foreach ($conditions as $whereNull) {
            if ($firstTime) {
                $query[] = trim($whereNull).' IS NULL';
                $firstTime = false;
            } else {
                $query[] = 'AND '.trim($whereNull).' IS NULL';
            }
        }

        return $query;
    }

    public function buildWhereNotNullQuery($conditions = array())
    {
        $query = array();

        if (!count($conditions)) {
            return $query;
        }

        $firstTime = false;
        if ($this->whereAdded === false) {
            $query[] = 'WHERE';
            $firstTime = true;
            $this->whereAdded = true;
        }

        foreach ($conditions as $whereNotNull) {
            if ($firstTime) {
                $query[] = trim($whereNotNull).' IS NOT NULL';
                $firstTime = false;
            } else {
                $query[] = 'AND '.trim($whereNotNull).' IS NOT NULL';
            }
        }

        return $query;
    }

    public function buildAllWhereQuery($where, $whereIn, $whereNotIn, $whereNull, $whereNotNull)
    {
        $query = array();
        $whereQuery = $this->buildWhereQuery($where);
        if (count($whereQuery)) {
            $query = array_merge($query, $whereQuery);
        }

        $whereInQuery = $this->buildWhereInQuery($whereIn);
        if (count($whereInQuery)) {
            $query = array_merge($query, $whereInQuery);
        }

        $whereNotInQuery = $this->buildWhereNotInQuery($whereNotIn);
        if (count($whereNotInQuery)) {
            $query = array_merge($query, $whereNotInQuery);
        }

        $whereNullQuery = $this->buildWhereNullQuery($whereNull);
        if (count($whereNullQuery)) {
            $query = array_merge($query, $whereNullQuery);
        }

        $whereNotNullQuery = $this->buildWhereNotNullQuery($whereNotNull);
        if (count($whereNotNullQuery)) {
            $query = array_merge($query, $whereNotNullQuery);
        }

        return $query;
    }
}
