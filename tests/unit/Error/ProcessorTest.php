<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   OpenMage
 * @package    OpenMage_Tests
 * @copyright  Copyright (c) 2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Error;

use Error_Processor as Subject;
use Generator;
use PHPUnit\Framework\TestCase;

class ProcessorTest extends TestCase
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
     * @group Error
     */
    public function testGetHostUrl(string $expectedResult, array $serverVars): void
    {
        foreach ($serverVars as $serverVar => $value) {
            $_SERVER[$serverVar] = $value;
        }
        $this->assertSame($expectedResult, $this->subject->getHostUrl());
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
