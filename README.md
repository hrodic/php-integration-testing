# PHP Integration Testing

Integration testing library in PHP for databases and other common infrastructure related tests.

It is developed as a set of extensions for PHPUnit that hooks on different events and executes your fixtures.

Currently you can run custom fixtures on the following PHPUnit hooks:

* BeforeFirstTest
* BeforeTest
* AfterTest
* AfterLastTest

## Road map

* RabbitMQ integration


## Requirements

[PHPUnit](https://phpunit.readthedocs.io/en/9.1)

## Installation

Via composer

```
composer require --dev hrodic/php-integration-testing
```

## Configuration

On PHPUnit configuration XML file you must specify the extension with its configuration.

If you need help with PHPUnit extensions, please refer to the [Official Documentation](https://phpunit.readthedocs.io/en/9.1/configuration.html#the-extensions-element)

As an example, look into phpunit-integration.xml.dist.


### PDO Driver

If you need to test the integration of MySQL or MariaDB, use the PDO driver extension.

```
<extensions>
    <extension class="IntegrationTesting\PHPUnit\Runner\Extension\PDODatabaseExtension">
        <arguments>
            <object class="IntegrationTesting\PHPUnit\Runner\Extension\PDODatabaseExtensionConfig">
                <arguments>
                    <array>
                        <element key="BEFORE_FIRST_TEST_PDO_FIXTURES_PATH">
                            <string>tests/fixtures/before-first-test</string>
                        </element>
                        <element key="BEFORE_TEST_PDO_FIXTURES_PATH">
                            <string>tests/fixtures/before-test</string>
                        </element>
                        <element key="AFTER_TEST_PDO_FIXTURES_PATH">
                            <string>tests/fixtures/after-test</string>
                        </element>
                        <element key="AFTER_LAST_TEST_PDO_FIXTURES_PATH">
                            <string>tests/fixtures/after-last-test</string>
                        </element>
                    </array>
                </arguments>
            </object>
            <object class="IntegrationTesting\Driver\PDOConnection">
                <arguments>
                    <string>mysql:host=localhost:3306;dbname=test;charset=utf8</string>
                    <string>test</string>
                    <string>test</string>
                </arguments>
            </object>
        </arguments>
    </extension>
</extensions>
```

The extension class is
```
IntegrationTesting\PHPUnit\Runner\Extension\PDODatabaseExtension
```

which requires a configuration and a PDO connection as arguments via XML config

The configuration class allows you to define in which paths your fixtures will be located

```
IntegrationTesting\PHPUnit\Runner\Extension\PDODatabaseExtensionConfig
```

The config keys to define each hook type are:

* BEFORE_FIRST_TEST_PDO_FIXTURES_PATH
* BEFORE_TEST_PDO_FIXTURES_PATH
* AFTER_TEST_PDO_FIXTURES_PATH
* AFTER_LAST_TEST_PDO_FIXTURES_PATH

The PDOConnection class is just a wrapper of the PDO PHP class.

```
IntegrationTesting\Driver\PDOConnection
```

It requires DSN, username and password of your database.

### RabbitMQ driver

@todo

## Fixture creation

A PDO fixture is just an SQL file. 

All the fixtures located in a specific hook category will be executed in order and inside a transaction.

How you create the SQL and the integrity of the database in each stage is up to you. The library does not force you
to follow any convention although is common to setup fixtures at the beginning and clean your mess after each test.

You can create, insert, delete or whatever you configure your user to do. Remember, your testing database must be isolated 
from any real database!

All four fixture hook types could be placed in the directory that you prefer.

For BeforeTest and AfterTest hooks, which occur in every specific test, you can also provide specific fixtures to be executed
just after the generic Before and After fixtures by implementing the interfaces WithBeforeTestFixtureName and/or WithAfterTestFixtureName.

```
final class YourIntegrationTest extends TestCase implements WithBeforeTestFixtureName, WithAfterTestFixtureName
{
    private const FIXTURE_NAME = 'pdo-integration-test';

    public static function getAfterTestFixtureName(): string
    {
        return self::FIXTURE_NAME;
    }

    public static function getBeforeTestFixtureName(): string
    {
        return self::FIXTURE_NAME;
    }

    public function testYourRepositoryHere(): void
    {
        // arrange
        // act
        // assert against real database (your fixtures are already there!)       
    }
}
```

The Extension will check if the methods are defined, and use them to locate subdirectories inside the main
BEFORE_TEST_PDO_FIXTURES_PATH and AFTER_TEST_PDO_FIXTURES_PATH directories.

### Execution flow

If you take a look onto the tests/fixtures folder, you will see an example on how you can organize your fixtures.
You can have multiple SQL files and the extension will read and execute them in order.

```
├── after-last-test                     # AFTER_LAST_TEST_PDO_FIXTURES_PATH, executed once, at the end
│   └── 01.sql
├── after-test                          # AFTER_TEST_PDO_FIXTURES_PATH, executed after each test
│   ├── 01.sql
│   └── pdo-integration-test            # executed after each test inside the class that defines this fixture name
│       └── 01.sql
├── before-first-test                   # BEFORE_FIRST_TEST_PDO_FIXTURES_PATH, executed once, at the beginning
│   └── 01.sql
└── before-test                         # BEFORE_TEST_PDO_FIXTURES_PATH, executed before each test
    ├── 01.sql
    └── pdo-integration-test            # executed before each test inside the class that defines this fixture name
        └── 01.sql
```

## Troubleshooting

Integration testing requires some infrastructure to be in place.

This library assumes (you can check docker-compose.yml file for inspiration) that you have an accessible database
or other infrastructure already in place and the database is created.