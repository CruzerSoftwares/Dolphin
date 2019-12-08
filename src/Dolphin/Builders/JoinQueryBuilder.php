<?php
/**
 * The Query builder API.
 *
 * @author RN Kushwaha <rn.kushwaha022@gmail.com>
 * @since v0.0.1 <Date: 16th April, 2019>
 */

namespace Dolphin\Builders;

/**
 * This class provides the mechanism to build the Where Queries.
 */
class JoinQueryBuilder extends QueryBuilder
{
    public function buildJoinQuery($conditions = array())
    {
        $prefix = $this->getPrefix();
        $query = array();

        if (!count($conditions)) {
            return $query;
        }

        foreach ($conditions as $join) {
            list($tableName, $tableAlias) = $this->addAlias($join[0]);

            if ($join[2] == '') {
                $query[] = 'JOIN '.$this->quote($prefix.$tableName).$tableAlias.' USING (`'.$join[1].'`)';
            } else {
                $query[] = 'JOIN '.$this->quote($prefix.$tableName).$tableAlias.' ON ('.$this->quote($join[1]).' '.$join[2].' '.$this->quote($join[3]).')';
            }
        }

        return $query;
    }

    public function buildLeftJoinQuery($conditions = array())
    {
        $prefix = $this->getPrefix();
        $query = array();

        if (!count($conditions)) {
            return $query;
        }

        foreach ($conditions as $leftJoin) {
            list($tableName, $tableAlias) = $this->addAlias($leftJoin[0]);

            if ($leftJoin[2] == '') {
                $query[] = 'LEFT JOIN '.$this->quote($prefix.$tableName).$tableAlias.' USING (`'.$leftJoin[1].'`)';
            } else {
                $query[] = 'LEFT JOIN '.$this->quote($prefix.$tableName).$tableAlias.' ON ('.$this->quote($leftJoin[1]).' '.$leftJoin[2].' '.$this->quote($leftJoin[3]).')';
            }
        }

        return $query;
    }

    public function buildRightJoinQuery($conditions = array())
    {
        $prefix = $this->getPrefix();
        $query = array();

        if (!count($conditions)) {
            return $query;
        }

        foreach ($conditions as $rightJoin) {
            list($tableName, $tableAlias) = $this->addAlias($rightJoin[0]);

            if ($rightJoin[2] == '') {
                $query[] = 'RIGHT JOIN '.$this->quote($prefix.$tableName).$tableAlias.' USING (`'.$rightJoin[1].'`)';
            } else {
                $query[] = 'RIGHT JOIN '.$this->quote($prefix.$tableName).$tableAlias.' ON ('.$this->quote($rightJoin[1]).' '.$rightJoin[2].' '.$this->quote($rightJoin[3]).')';
            }
        }

        return $query;
    }

    public function buildCrossJoinQuery($conditions = array())
    {
        $prefix = $this->getPrefix();
        $query = array();

        if (!count($conditions)) {
            return $query;
        }
        
        foreach ($conditions as $crossJoin) {
            list($tableName, $tableAlias) = $this->addAlias($crossJoin[0]);

            $query[] = 'CROSS JOIN '.$this->quote($prefix.$tableName).$tableAlias;
        }

        return $query;
    }

    public function buildAllJoinQuery($join, $leftJoin, $rightJoin, $crossJoin)
    {
        $query = array();

        $joinQuery = $this->buildJoinQuery($join);
        if (count($joinQuery)) {
            $query = array_merge($query, $joinQuery);
        }

        $leftJoinQuery = $this->buildLeftJoinQuery($leftJoin);
        if (count($leftJoinQuery)) {
            $query = array_merge($query, $leftJoinQuery);
        }

        $rightJoinQuery = $this->buildRightJoinQuery($rightJoin);
        if (count($rightJoinQuery)) {
            $query = array_merge($query, $rightJoinQuery);
        }

        $crossJoinQuery = $this->buildCrossJoinQuery($crossJoin);
        if (count($crossJoinQuery)) {
            $query = array_merge($query, $crossJoinQuery);
        }

        return $query;
    }
}
