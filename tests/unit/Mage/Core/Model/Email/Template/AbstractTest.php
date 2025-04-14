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

namespace unit\Mage\Core\Model\Email\Template;

use Generator;
use Mage;
use Mage_Core_Model_Email_Template_Abstract as Subject;
use PHPUnit\Framework\TestCase;

class AbstractTest extends TestCase
{
    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        Mage::app();
    }

    public function setUp(): void
    {
        self::$subject = $this->getMockForAbstractClass(Subject::class);
    }

    /**
     * @dataProvider provideValidateFileExension
     * @group Mage_Core
     * @group Mage_Core_Model
     */
    public function testValidateFileExension(bool $expectedResult, string $filePath, string $extension, bool $fileExists): void
    {
        if ($fileExists) {
            static::assertFileExists($filePath);
        } else {
            static::assertFileDoesNotExist($filePath);
        }

        static::assertSame($expectedResult, self::$subject->validateFileExension($filePath, $extension));
    }

    public function provideValidateFileExension(): Generator
    {
        yield 'css file exists' => [
            true,
            $_SERVER['TEST_ROOT'] . '/unit/fixtures/files/test.css',
            'css',
            true,
        ];
        yield 'css file exists, but empty' => [
            false,
            $_SERVER['TEST_ROOT'] . '/unit/fixtures/files/test-empty.css',
            'css',
            true,
        ];
        yield 'css file not exists' => [
            false,
            $_SERVER['TEST_ROOT'] . '/unit/fixtures/files/test.not-exist',
            'css',
            false,
        ];
    }
}
