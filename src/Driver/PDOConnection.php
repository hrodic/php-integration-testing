<?php declare(strict_types=1);

namespace IntegrationTesting\Driver;

use PDO;

/**
 * Class PDOConnection
 * @package IntegrationTesting\Driver
 * @internal NOT FOR PUBLIC USE
 */
class PDOConnection
{
    private $PDO;

    public function __construct(string $dsn, string $username, string $password)
    {
        $this->PDO = new PDO($dsn, $username, $password);
        $this->PDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->PDO->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    }

    public function PDO(): PDO
    {
        return $this->PDO;
    }
}
