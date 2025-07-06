<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Rule\Model;

use Composer\InstalledVersions;
use Error;
use Mage_Core_Exception;
use Mage_Rule_Model_Abstract as Subject;
use Mage_Rule_Model_Action_Collection;
use Mage_Rule_Model_Condition_Combine;
use OpenMage\Tests\Unit\Traits\DataProvider\Base\BoolTrait;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Rule\RuleTrait;
use OpenMage\Tests\Unit\OpenMageTest;
use Varien_Data_Form;
use Varien_Db_Select;
use Varien_Object;

final class AbstractTest extends OpenMageTest
{
    use BoolTrait;
    use RuleTrait;

    public const CALL_TO_A_MEMBER_FUNCTION_SET_RULE_ON_NULL = 'Call to a member function setRule() on null';

    private static Subject $subject;

    public function setUp(): void
    {
        self::$subject = $this->getMockForAbstractClass(Subject::class);
    }

    /**
     * @group Model
     */
    public function testGetProductFlatSelect(): void
    {
        try {
            static::assertInstanceOf(Varien_Db_Select::class, self::$subject->getProductFlatSelect(0));
        } catch (Mage_Core_Exception $exception) {
            static::assertSame('Resource is not set.', $exception->getMessage());
        }
    }

    /**
     * @dataProvider provideBool
     * @group Model
     */
    public function testGetConditions(bool $empty): void
    {
        if (!$empty) {
            self::$subject->setConditions(new Mage_Rule_Model_Condition_Combine());
        }

        try {
            static::assertInstanceOf(Mage_Rule_Model_Condition_Combine::class, self::$subject->getConditions());
        } catch (Error $error) {
            static::assertSame(self::CALL_TO_A_MEMBER_FUNCTION_SET_RULE_ON_NULL, $error->getMessage());
        }
    }

    /**
     * @dataProvider provideBool
     * @group Model
     */
    public function testGetActions(bool $empty): void
    {
        if (!$empty) {
            self::$subject->setActions(new Mage_Rule_Model_Action_Collection());
        }

        try {
            static::assertInstanceOf(Mage_Rule_Model_Action_Collection::class, self::$subject->getActions());
        } catch (Error $error) {
            static::assertSame(self::CALL_TO_A_MEMBER_FUNCTION_SET_RULE_ON_NULL, $error->getMessage());
        }
    }

    /**
     * @group Model
     */
    public function testGetForm(): void
    {
        static::assertInstanceOf(Varien_Data_Form::class, self::$subject->getForm());
    }

    /**
     * @group Model
     */
    public function testLoadPost(array $data = []): void
    {
        static::assertInstanceOf(Subject::class, self::$subject->loadPost($data));
    }

    /**
     * @covers Mage_Rule_Model_Abstract::validate()
     * @dataProvider provideValidateData
     * @group Model
     */
    public function testValidate(bool|array $expectedResul, ?array $data = null): void
    {
        $object = new Varien_Object($data);
        try {
            static::assertSame($expectedResul, self::$subject->validate($object));
        } catch (Error $error) {
            static::assertSame(self::CALL_TO_A_MEMBER_FUNCTION_SET_RULE_ON_NULL, $error->getMessage());
        }

    }

    /**
     * @dataProvider provideValidateData
     * @group Model
     */
    public function testValidateData(bool|array $expectedResul, ?array $data = null): void
    {
        if (PHP_VERSION_ID >= 80300 && version_compare(InstalledVersions::getPrettyVersion('shardj/zf1-future'), '1.24.2', '<=')) {
            static::markTestSkipped('see https://github.com/Shardj/zf1-future/pull/465');
        }
        $object = new Varien_Object($data);
        static::assertSame($expectedResul, self::$subject->validateData($object));
    }

    /**
     * @covers Mage_Rule_Model_Abstract::isDeleteable()
     * @group Model
     */
    public function testIsDeleteable(): void
    {
        static::assertIsBool(self::$subject->isDeleteable());
    }

    /**
     * @covers Mage_Rule_Model_Abstract::setIsDeleteable()
     * @dataProvider provideBool
     * @group Model
     */
    public function testSetIsDeleteable(bool $value): void
    {
        static::assertInstanceOf(Subject::class, self::$subject->setIsDeleteable($value));
    }

    /**
     * @covers Mage_Rule_Model_Abstract::isReadonly()
     * @group Model
     */
    public function testIsReadonly(): void
    {
        static::assertIsBool(self::$subject->isReadonly());
    }

    /**
     * @covers Mage_Rule_Model_Abstract::setIsReadonly()
     * @dataProvider provideBool
     * @group Model
     */
    public function testSetIsReadonly(bool $value): void
    {
        static::assertInstanceOf(Subject::class, self::$subject->setIsReadonly($value));
    }

    /**
     * @group Model
     */
    public function testGetWebsiteIds(): void
    {
        try {
            static::assertIsArray(self::$subject->getWebsiteIds());
        } catch (Mage_Core_Exception $exception) {
            static::assertSame('Resource is not set.', $exception->getMessage());
        }
    }

    /**
     * @covers Mage_Rule_Model_Abstract::asString()
     * @group Model
     */
    public function testAsString(): void
    {
        static::assertSame('', self::$subject->asString());
    }

    /**
     * @covers Mage_Rule_Model_Abstract::asHtml()
     * @group Model
     */
    public function testAsHtml(): void
    {
        static::assertSame('', self::$subject->asHtml());
    }

    /**
     * @covers Mage_Rule_Model_Abstract::asArray()
     * @group Model
     */
    public function testAsArray(): void
    {
        static::assertSame([], self::$subject->asArray());
    }
}
