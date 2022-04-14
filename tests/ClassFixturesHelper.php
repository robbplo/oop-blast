<?php declare(strict_types=1);

use Symfony\Component\Yaml\Yaml;
use function PHPUnit\Framework\assertArrayHasKey;
use function PHPUnit\Framework\assertEquals;

/**
 * This class contains helpers for testing expected class structure.
 */
class ClassFixturesHelper
{
    private array $fixtures = [];

    public function __construct()
    {
        foreach (glob(__DIR__ . '/fixtures/*.yaml') as $file) {
            $this->fixtures += Yaml::parse(file_get_contents($file));
        }
    }

    /**
     * Voor elke method in de fixture wordt het volgende bevestigd:
     * - De class bevat alle gespecificeerde properties met het juiste type
     * - De class bevat een method met de juiste naam
     * - Elke parameter in de fixture bestaat op de method
     * - Elke method parameter heeft het juiste type
     */
    public function assertMatches(string $path, string $class): void
    {
        $shortName = (new ReflectionClass($class))->getShortName();
        $fixture = $this->fixtures[$shortName];
        $reflection = $this->reflect($path, $class);

        foreach ($fixture['properties'] as $name => $type) {
            assertArrayHasKey($name, $reflection['properties'], "De class moet de property `$name` bevatten.");
            assertEquals($type, $reflection['properties'][$name], "De class property `$name` moet van het type `$type` zijn.");
        }

        foreach ($fixture['methods'] as $method => $params) {
            assertArrayHasKey($method, $reflection['methods'], "De class moet de method `$method` bevatten.");

            $reflectedMethod = $reflection['methods'][$method];
            foreach ($params as $name => $type) {
                assertArrayHasKey($name, $reflectedMethod, "De methode `$method` moet de parameter `$name` bevatten.");
                assertEquals($type, $reflectedMethod[$name], "De parameter `$name` op de methode `$method` moet van het type `$type` zijn.");
            }
        }
    }

    /**
     * Reflect class and parse it into the same structure as the yaml.
     */
    private function reflect(string $path, string $class)
    {
        require_once $path;
        $reflection = new ReflectionClass($class);

        $properties = $methods = [];
        foreach ($reflection->getProperties() as $property) {
            $properties[$property->name] = $property->getType()->getName();
        }
        foreach ($reflection->getMethods() as $method) {
            $params = [];
            foreach ($method->getParameters() as $param) {
                $params[$param->name] = $param->getType()->getName();
            }
            $methods[$method->name] = $params;
        }

        return compact('properties', 'methods');
    }
}