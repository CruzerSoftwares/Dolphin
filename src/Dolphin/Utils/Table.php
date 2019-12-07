<?php
/**
 * The Helper class to get Table Name
 *
 * @author RN Kushwaha <rn.kushwaha022@gmail.com>
 * @since v0.0.5 <Date: 8th Dec, 2019>
 */

namespace Dolphin\Utils;

use Dolphin\Builders\QueryBuilder;

class Table
{
    /**
     * It returns the table name to Query from
     * Used internally
     */
    public function getTable($class)
    {
        $ref   = new \ReflectionClass($class);
        $qb    = new QueryBuilder();
        $util  = new Utils();

        if ($ref->hasProperty('table')) {
            $tableCheck = $ref->getProperty('table');
            $tableCheck->setAccessible(true);
            $tableName = $tableCheck->getValue(new $class());
        } else {
            $tableName = explode('\\', $class);
            $tableName = $util->decamelize(end($tableName));
        }

        $prefix = $qb->getPrefix();
        $table = $prefix.$tableName;

        return $table;
    }
}
