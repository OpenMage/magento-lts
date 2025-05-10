<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
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
     * @group Helper
     */
    public function testDecodeGridSerializedInput(array $expectedResult, string $encoded): void
    {
        static::assertSame($expectedResult, self::$subject->decodeGridSerializedInput($encoded));
    }
}
