<?php

declare(strict_types=1);

namespace Tests\Support;

use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionMethod;
use wwaz\Components\Config;
use wwaz\Components\Factory;

abstract class ProjectTestCase extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->setStaticProperty(Config::class, 'config', []);
        Config::set('global', require dirname(__DIR__, 2) . '/config/config.php');

        $this->setStaticProperty(Factory::class, 'cache', []);
        $this->setStaticProperty(Factory::class, 'componentCache', []);
        $this->setStaticProperty(Factory::class, 'namespaces', ['wwaz\\Components']);
        $this->setStaticProperty(Factory::class, 'throwErrors', false);

        Factory::addNamespace('wwaz\\Components\\Componenttest');
    }

    protected function setStaticProperty(string $className, string $property, mixed $value): void
    {
        $reflection = new ReflectionClass($className);
        $propertyReflection = $reflection->getProperty($property);
        $propertyReflection->setValue(null, $value);
    }

    protected function getStaticProperty(string $className, string $property): mixed
    {
        $reflection = new ReflectionClass($className);
        $propertyReflection = $reflection->getProperty($property);

        return $propertyReflection->getValue();
    }

    protected function invokeMethod(object|string $target, string $method, array $arguments = []): mixed
    {
        $reflectionMethod = new ReflectionMethod($target, $method);

        if (is_string($target)) {
            return $reflectionMethod->invokeArgs(null, $arguments);
        }

        return $reflectionMethod->invokeArgs($target, $arguments);
    }
}
