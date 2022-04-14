<?php

use PHPUnit\Framework\TestCase;
use Robbinploeger\OopTesting\Logger;

class LoggerTest extends TestCase
{
    private ClassFixturesHelper $helper;

    public function setUp(): void
    {
        parent::setUp();

        $this->helper = new ClassFixturesHelper();
    }

    /** @test */
    public function test_class_matches_fixture()
    {
        $this->helper->assertMatches(__DIR__ . '/../src/Logger.php', Logger::class);
    }
}
