<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Error;

use Error_Processor as Subject;
use Generator;
use PHPUnit\Framework\TestCase;

final class ProcessorTest extends TestCase
{
    public Subject $subject;

    public array $server;

    public function setUp(): void
    {
        $this->subject = new Subject();
        $this->server  = $_SERVER;
    }

    public function tearDown(): void
    {
        $_SERVER = $this->server;
    }

    /**
     * @dataProvider provideGetHostUrl
     */
    public function testGetHostUrl(string $expectedResult, array $serverVars): void
    {
        foreach ($serverVars as $serverVar => $value) {
            $_SERVER[$serverVar] = $value;
        }

        self::assertSame($expectedResult, $this->subject->getHostUrl());
    }

    public function provideGetHostUrl(): Generator
    {
        yield 'default' => [
            'http://localhost',
            [],
        ];
        yield 'port 80' => [
            'http://localhost',
            [
                'SERVER_PORT' => 80,
            ],
        ];
        yield 'port 8000' => [
            'http://localhost:8000',
            [
                'SERVER_PORT' => 8000,
            ],
        ];
        yield 'name with port + port 8000' => [
            'http://localhost:8000',
            [
                'SERVER_NAME' => 'localhost:8000',
                'SERVER_PORT' => 8000,
            ],
        ];
    }
}
