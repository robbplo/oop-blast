<?php

use PHPUnit\Framework\TestCase;
use Robbinploeger\OopTesting\Logger;

class LoggerTest extends TestCase
{
    /** @test */
    public function test_class_matches_fixture()
    {
        // Include uitwerking van student
        include(__DIR__ . '/../src/Logger.php');
        // Class gebruikt voor dit voorbeeld:
        //
        //class Logger {
        //    public function log(string $message): void
        //    {
        //        echo $message;
        //    }
        //}


        // Definitie van vereisten van de class
        $fixture = [
            'methods' => [
                'log' => [
                    'parameters' => [
                        'message' => [
                            'type' => 'string',
                        ],
                    ],
                ],
            ],
            'properties' => [],
        ];

        $reflectionClass = new ReflectionClass(Logger::class);

        /**
         * Voor elke method in de fixture wordt het volgende bevestigd:
         * - De class bevat een method met de juiste naam
         * - De method in de class heeft evenveel parameters als de fixture
         * - Elke parameter in de fixture bestaat op de method
         * - Elek parameter heeft het juiste type
         */
        foreach ($fixture['methods'] as $methodName => $methodFixture) {
            $this->assertTrue($reflectionClass->hasMethod($methodName), "De class moet de methode `$methodName` bevatten");
            $reflectionMethod = $reflectionClass->getMethod($methodName);
            $reflectionParameters = $reflectionMethod->getParameters();

            $expectedParameterCount = count($methodFixture['parameters']);
            $this->assertCount($expectedParameterCount, $reflectionParameters, "De methode `$methodName` moet $expectedParameterCount parameters bevatten.");

            foreach ($methodFixture['parameters'] as $parameterName => $parameterFixture) {
                // Vind de juiste ReflectionParameter. Dit zou een stuk makkelijker zijn als de lijst van parameters een associative array was...
                $reflectionParameter = array_reduce(
                    $reflectionParameters,
                    function ($result, $currentParameter) use ($parameterName) {
                        if ($result instanceof ReflectionParameter) {
                            return $currentParameter;
                        }

                        if ($currentParameter->getName() === $parameterName) {
                            return $currentParameter;
                        }

                        return null;
                    });

                $this->assertNotNull($reflectionParameter, "De methode `$methodName` moet de parameter `$parameterName` bevatten");

                // De juiste parameter bestaat. Check nu dat het juiste type ook bestaat.
                $expectedParameterType = $parameterFixture['type'];
                $this->assertEquals(
                    $expectedParameterType,
                    $reflectionParameter->getType()?->getName(),
                    "De parameter `$parameterName` op de methode `$methodName` moet van het type `$expectedParameterType` zijn."
                );
            }
        }
    }
}
