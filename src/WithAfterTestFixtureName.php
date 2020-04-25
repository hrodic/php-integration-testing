<?php declare(strict_types=1);

namespace IntegrationTesting;

interface WithAfterTestFixtureName
{
    /**
     * PDODatabaseExtension will try to get the fixture name
     *
     * @return string
     */
    public static function getAfterTestFixtureName(): string;
}
