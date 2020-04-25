# PHP Integration Testing

Integration testing library in PHP for databases and other common infrastructure related tests.

[![Build Status](https://travis-ci.com/hrodic/php-integration-testing.svg?branch=master)](https://travis-ci.com/hrodic/php-integration-testing)

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

You are able to specify the configuration filename that you will be using. Defaults to .integration-testing.json

```
<extensions>
    <extension class="IntegrationTesting\PHPUnit\Runner\Extension\Handler">
        <arguments>
            <string>.integration-testing.json</string>
        </arguments>
    </extension>
</extensions>
```

You also check phpunit-integration.xml.dist example

If you need help with PHPUnit extensions, please refer to the [Official Documentation](https://phpunit.readthedocs.io/en/9.1/configuration.html#the-extensions-element)

### PDO Fixtures

If you need to test the integration of MySQL or MariaDB, use the PDO driver extension.

It requires configuration parameters that can be found in the json config file.

The most important parameters are DSN, username and password of your database + some fixture path definitions.

Example: 
```
"pdo": {
    "dsn": "mysql:host=localhost:3306;dbname=test;charset=utf8",
    "user": "test",
    "password": "test",
    "fixtures": {
      "beforeFirstTest": {
        "path": "tests/fixtures/before-first-test",
        "extension": "sql"
      },
      "beforeTest": {
        "path": "tests/fixtures/before-test",
        "extension": "sql"
      },
      "afterTest": {
        "path": "tests/fixtures/after-test"
      },
      "afterLastTest": {
        "path": "tests/fixtures/after-last-test"
      }
    }
},
```

### RabbitMQ fixtures

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