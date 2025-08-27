<?php

namespace Gazelle\DB {
    class DummyMysqli extends \mysqli {
        public function __construct() {}
        public function __destruct() {}
    }
    $mysqli_connect_calls = [];
    function mysqli_connect($host, $user, $pass, $database, $port, $socket) {
        global $mysqli_connect_calls;
        $mysqli_connect_calls[] = func_get_args();
        if (count($mysqli_connect_calls) === 1) {
            throw new \mysqli_sql_exception('Connection refused', 2002);
        }
        return new DummyMysqli();
    }
    function mysqli_connect_errno() { return 2002; }
    function mysqli_connect_error() { return 'Connection refused'; }
    function gethostbyname($host) { return '10.0.0.1'; }
}

namespace Gazelle\DBTest {
    use Gazelle\DB\Mysql;
    use PHPUnit\Framework\TestCase;

    class MysqlConnectTest extends TestCase {
        public function testLocalhostUsesTcpWithDockerFallback() {
            global $mysqli_connect_calls;
            $db = new Mysql('gazelle', 'user', 'pass', 'localhost', 36000, null);
            $db->connect();
            \PHPUnit\Framework\Assert::assertCount(2, $mysqli_connect_calls);
            \PHPUnit\Framework\Assert::assertSame('127.0.0.1', $mysqli_connect_calls[0][0]);
            \PHPUnit\Framework\Assert::assertSame('10.0.0.1', $mysqli_connect_calls[1][0]);
            \PHPUnit\Framework\Assert::assertSame(36000, $mysqli_connect_calls[1][4]);
        }
    }
}
