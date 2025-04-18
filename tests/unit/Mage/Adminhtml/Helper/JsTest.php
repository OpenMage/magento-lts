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

namespace OpenMage\Tests\Unit\Mage\Adminhtml\Helper;

use Mage;
use Mage_Adminhtml_Helper_Js as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Adminhtml\Helper\JsTrait;

class JsTest extends OpenMageTest
{
    use JsTrait;

    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::helper('adminhtml/js');
    }

    /**
     * @covers Mage_Adminhtml_Helper_Js::decodeGridSerializedInput()
     * @dataProvider provideDecodeGridSerializedInput
     * @group Mage_Adminhtml
     * @group Mage_Adminhtml_Helper
     */
    public function testDecodeGridSerializedInput(array $expectedResult, string $encoded): void
    {
        static::assertSame($expectedResult, self::$subject->decodeGridSerializedInput($encoded));
    }
}
