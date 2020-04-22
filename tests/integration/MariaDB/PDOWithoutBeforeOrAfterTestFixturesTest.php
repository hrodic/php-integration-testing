<?php declare(strict_types=1);

namespace IntegrationTesting\Tests\Integration;

use IntegrationTesting\Driver\PDOConnection;
use PHPUnit\Framework\TestCase;

/**
 * @covers \IntegrationTesting\PHPUnit\Runner\Extension\PDODatabaseExtension
 * @uses   \IntegrationTesting\Driver\PDOConnection
 */
final class PDOWithoutBeforeOrAfterTestFixturesTest extends TestCase
{

    public function testReadingEphemeralTableHasNoContents(): void
    {
        $conn = new PDOConnection(
            constant('DB_DSN'),
            constant('DB_USERNAME'),
            constant('DB_PASSWORD')
        );
        $statement = $conn->PDO()->query("SELECT * FROM  `test`.`ephemeral_table`");
        $this->assertSame(0, $statement->rowCount());
    }

    public function testPersistentTableHasRowCountEqualsToTestsExecuted(): void
    {
        $conn = new PDOConnection(
            constant('DB_DSN'),
            constant('DB_USERNAME'),
            constant('DB_PASSWORD')
        );
        $statement = $conn->PDO()->query("SELECT * FROM  `test`.`persistent_table`");
        // this is the third test to run a beforeTest hook!
        $this->assertSame(3, $statement->rowCount());
    }
}