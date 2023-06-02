<?php

declare(strict_types=1);

namespace ASanikovich\LaravelSpatial\Database;

use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\MySqlConnection;
use PDO;

final class Connection
{
    /**
     * @codeCoverageIgnore
     */
    public function isSupportAxisOrder(ConnectionInterface $connection): bool
    {
        /** @var MySqlConnection $connection */
        if ($this->isMariaDb($connection)) {
            return false;
        }

        if ($this->isMySql57($connection)) {
            return false;
        }

        return true;
    }

    private function isMariaDb(MySqlConnection $connection): bool
    {
        return $connection->isMaria();
    }

    private function isMySql57(MySqlConnection $connection): bool
    {
        /** @var string $version */
        $version = $connection->getPdo()->getAttribute(PDO::ATTR_SERVER_VERSION);

        return version_compare($version, '5.8.0', '<');
    }
}
