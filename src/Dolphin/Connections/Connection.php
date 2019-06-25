<?php
/**
 * This class provide the pdo connection object.
 *
 * @author RN Kushwaha <rn.kushwaha022@gmail.com>
 *
 * @since v0.0.2 <Date: 29th April, 2019>
 */

namespace Dolphin\Connections;

use App\Config\Credentials;

class Connection
{
    private static $con = null;

    private function __construct()
    {
    }

    public static function getPrefix()
    {
        return Credentials::get('prefix');
    }

    public static function get()
    {
        if (!self::$con) {
            self::$con = new \PDO('mysql:host=localhost; dbname='.Credentials::get('database'),
                                    Credentials::get('username'),
                                    Credentials::get('password')
                                );
            self::$con->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
            // which tells PDO to disable emulated prepared statements and use real prepared statements.
            // This makes sure the statement and the values aren't parsed by PHP before sending it to the
            // MySQL server (giving a possible attacker no chance to inject malicious SQL).
            self::$con->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        }

        return self::$con;
    }

    private function __clone()
    {
    }
}
