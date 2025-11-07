<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Adminhtml\Model;

use Mage;
use Mage_Adminhtml_Model_LayoutUpdate_Validator as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Adminhtml\Model\LayoutUpdate\ValidatorTrait;
use Varien_Simplexml_Element;

final class ValidatorTest extends OpenMageTest
{
    use ValidatorTrait;

    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::getModel('adminhtml/layoutUpdate_validator');
    }

    /**
     * @covers Mage_Adminhtml_Model_LayoutUpdate_Validator::getMessages()
     * @group Model
     */
    public function testGetMessages(): void
    {
        self::assertIsArray(self::$subject->getMessages());
    }

    /**
     * @covers Mage_Adminhtml_Model_LayoutUpdate_Validator::isValid()
     * @dataProvider provideIsValidData
     * @group Model
     */
    public function testIsValid(bool $expectedResult, string|Varien_Simplexml_Element $data): void
    {
        self::assertSame($expectedResult, self::$subject->isValid($data));
    }
}
