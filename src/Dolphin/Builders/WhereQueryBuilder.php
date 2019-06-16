<?php
/**
 * The Query builder API.
 *
 * @author RN Kushwaha <rn.kushwaha022@gmail.com>
 *
 * @since v0.0.1 <Date: 16th April, 2019>
 */

namespace Dolphin\ORM\Builders;

/**
 * This class provides the mechanism to build the Where Queries.
 */
class WhereQueryBuilder extends QueryBuilder
{
    protected function prepareArrayForWhere($bindKey, $bindVal = null){
        $ar = $conditionAr = array();
        // expecting a string like 'status = :status'
        if ($this->checkWherePrepareUsed($bindKey)) {
            $conditionAr = preg_split('/:/', $bindKey);
        }

        // taking the second part just after :
        if (is_array($conditionAr) && count($conditionAr)) {
            $ar[':'.$conditionAr[1]] = $bindVal;
        }
        
        return $ar;
    }
    
    public function parseWhereQuery($whereQuery = [])
    {
        $ar = array();
        
        foreach ($whereQuery as $where) {
            if (is_array($where[1])) {
                foreach ($where[1] as $key => $value) {
                    $ar[':'.$key] = $value;
                }
            } elseif ($where[1] != '') {
                $arNext = $this->prepareArrayForWhere($where[0], $where[1]);
                if (count($arNext)) {
                    $ar = array_merge($ar, $arNext);
                }
            }
        }

        return $ar;
    }

    public function checkWherePrepareUsed($condition)
    {
        return preg_match('/:+[a-zA-Z_]/', $condition);
    }

    public function buildWhereQuery($conditions = array())
    {
        $query = array();

        if (count($conditions)) {
            $firstTime = true;
            $query[] = 'WHERE';
            $this->whereAdded = true;

            foreach ($conditions as $where) {
                if ($firstTime) {
                    $query[] = $where[0];
                    $firstTime = false;
                } else {
                    $query[] = 'AND '.$where[0];
                }
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

        if (count($conditions)) {
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
                    $query[] = $whereIn[0].' IN ('.$dataStr.')';
                    $firstTime = false;
                } else {
                    $query[] = 'AND '.$whereIn[0].' IN ('.$dataStr.')';
                }
            }
        }

        return $query;
    }

    public function buildWhereNotInQuery($conditions = array())
    {
        $query = array();

        if (count($conditions)) {
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
                    $query[] = $whereNotIn[0].' NOT IN ('.$dataStr.')';
                    $firstTime = false;
                } else {
                    $query[] = 'AND '.$whereNotIn[0].' NOT IN ('.$dataStr.')';
                }
            }
        }

        return $query;
    }

    public function buildWhereNullQuery($conditions = array())
    {
        $query = array();

        if (count($conditions)) {
            $firstTime = false;
            if ($this->whereAdded === false) {
                $query[] = 'WHERE';
                $firstTime = true;
                $this->whereAdded = true;
            }

            foreach ($conditions as $whereNull) {
                if ($firstTime) {
                    $query[] = $whereNull.' IS NULL';
                    $firstTime = false;
                } else {
                    $query[] = 'AND '.$whereNull.' IS NULL';
                }
            }
        }

        return $query;
    }

    public function buildWhereNotNullQuery($conditions = array())
    {
        $query = array();

        if (count($conditions)) {
            $firstTime = false;
            if ($this->whereAdded === false) {
                $query[] = 'WHERE';
                $firstTime = true;
                $this->whereAdded = true;
            }

            foreach ($conditions as $whereNotNull) {
                if ($firstTime) {
                    $query[] = $whereNotNull.' IS NOT NULL';
                    $firstTime = false;
                } else {
                    $query[] = 'AND '.$whereNotNull.' IS NOT NULL';
                }
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
