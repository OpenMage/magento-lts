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

namespace OpenMage\Tests\Unit\Mage\Core\Model\Email\Template;

use Mage_Core_Model_Email_Template_Abstract as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Core\Model\Email\Template\AbstractTrait;

final class AbstractTest extends OpenMageTest
{
    use AbstractTrait;

    /** @phpstan-ignore property.onlyWritten */
    private static Subject $subject;

    public function setUp(): void
    {
        self::$subject = $this->getMockForAbstractClass(Subject::class);
    }

    /**
     * @dataProvider provideValidateFileExension
     * @group Model
     */
    public function testValidateFileExension(bool $expectedResult, string $filePath, string $extension, bool $fileExists): void
    {
        if ($fileExists) {
            static::assertFileExists($filePath);
        } else {
            static::assertFileDoesNotExist($filePath);
        }

        static::markTestSkipped('wait...');
        /** @phpstan-ignore deadCode.unreachable */
        static::assertSame($expectedResult, self::$subject->validateFileExension($filePath, $extension));
    }
}
