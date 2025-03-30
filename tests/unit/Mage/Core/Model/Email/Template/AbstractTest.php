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
    public Subject $subject;

    public function setUp(): void
    {
        Mage::app();
        $this->subject = $this->getMockForAbstractClass(Subject::class);
    }

    /**
     * @dataProvider provideValidateFileExension
     * @group Mage_Core
     * @group Mage_Core_Model
     */
    public function testValidateFileExension(bool $expectedResult, string $extension, string $filePath): void
    {
        if ($expectedResult) {
            $this->assertFileExists($filePath);
        } else {
            $this->assertFileDoesNotExist($filePath);
        }

        $this->assertSame($expectedResult, $this->subject->validateFileExension($extension, $filePath));
    }

    public function provideValidateFileExension(): Generator
    {
        yield 'css file exists' => [
            true,
            'css',
            __DIR__ . '/../../../../../fixtures/files/test.css',
        ];
        yield 'css file not exists' => [
            false,
            'css',
            __DIR__ . '/../../../../../fixtures/files/test.not-exist',
        ];
    }
}
