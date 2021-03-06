<?php
/**
 * The Query builder API.
 *
 * @author RN Kushwaha <rn.kushwaha022@gmail.com>
 * @since v0.0.1 <Date: 16th April, 2019>
 */

namespace Dolphin\Parsers;
/**
 * This class provides the mechanism to build the Where Queries.
 */
class WhereQueryParser
{

    protected function prepareArrayForWhere($bindKey, $bindVal = null, $mainAr = []){
        $ar = $conditionAr = [];
        // expecting a string like 'status = :status'
        if ($this->checkWherePrepareUsed($bindKey)) {
            $conditionAr = preg_split('/:/', $bindKey);
        }

        // taking the second part just after :
        if (is_array($conditionAr) && count($conditionAr)) {
            $ar[':'.$conditionAr[1]] = $bindVal;
        }

        if (count($ar)) {
            $mainAr = array_merge($mainAr, $ar);
        }

        return $mainAr;
    }

    public function parseWhereQuery($whereQuery = [])
    {
        $ar = [];
        if(!count($whereQuery)){
          return $ar;
        }

        foreach ($whereQuery as $where) {
            if (is_array($where[1])) {
                foreach ($where[1] as $key => $value) {
                    $ar[':'.$key] = $value;
                }
            } elseif ($where[1] != '') {
                $ar = $this->prepareArrayForWhere($where[0], $where[1], $ar);
            }
        }

        return $ar;
    }

    public function checkWherePrepareUsed($condition)
    {
        return preg_match('/:+[a-zA-Z_]/', $condition);
    }
}
