<?php declare(strict_types=1);

namespace IntegrationTesting\PHPUnit\Runner\Extension;

use PHPUnit\Runner\BeforeFirstTestHook;
use PHPUnit\Runner\BeforeTestHook;
use PHPUnit\Runner\AfterTestHook;
use PHPUnit\Runner\AfterLastTestHook;

interface FixtureLoader extends BeforeFirstTestHook, BeforeTestHook, AfterTestHook, AfterLastTestHook
{

}
