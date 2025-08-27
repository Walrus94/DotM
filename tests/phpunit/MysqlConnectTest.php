<?php

namespace Gazelle\DB {
    class DummyMysqli extends \mysqli {
        public function __construct() {}
        public function __destruct() {}
    }
    $mysqli_connect_args = [];
    function mysqli_connect($host, $user, $pass, $database, $port, $socket) {
        global $mysqli_connect_args;
        $mysqli_connect_args = func_get_args();
        return new DummyMysqli();
    }
    function mysqli_connect_errno() { return 0; }
    function mysqli_connect_error() { return ''; }
}

namespace Gazelle\DBTest {
    use Gazelle\DB\Mysql;
    use PHPUnit\Framework\TestCase;

    class MysqlConnectTest extends TestCase {
        public function testLocalhostUsesTcp() {
            global $mysqli_connect_args;
            $db = new Mysql('gazelle', 'user', 'pass', 'localhost', 36000, null);
            $db->connect();
            \PHPUnit\Framework\Assert::assertSame('127.0.0.1', $mysqli_connect_args[0]);
            \PHPUnit\Framework\Assert::assertSame(36000, $mysqli_connect_args[4]);
        }
    }
}
