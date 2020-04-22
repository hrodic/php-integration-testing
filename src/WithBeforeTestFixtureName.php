<?php declare(strict_types=1);

namespace IntegrationTesting;

interface WithBeforeTestFixtureName
{
    /**
     * PDODatabaseExtension will try to get the fixture name
     * @return string
     */
    public static function getBeforeTestFixtureName(): string;
}
