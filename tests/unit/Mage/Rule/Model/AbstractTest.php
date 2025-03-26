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
 * @copyright  Copyright (c) 2025 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Rule\Model;

use Composer\InstalledVersions;
use Error;
use Mage;
use Mage_Core_Exception;
use Mage_Rule_Model_Abstract as Subject;
use Mage_Rule_Model_Action_Collection;
use Mage_Rule_Model_Condition_Combine;
use OpenMage\Tests\Unit\Traits\DataProvider\Base\BoolTrait;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Rule\RuleTrait;
use PHPUnit\Framework\TestCase;
use Varien_Data_Form;
use Varien_Db_Select;
use Varien_Object;

class AbstractTest extends TestCase
{
    use BoolTrait;
    use RuleTrait;

    public const CALL_TO_A_MEMBER_FUNCTION_SET_RULE_ON_NULL = 'Call to a member function setRule() on null';

    public Subject $subject;

    public function setUp(): void
    {
        Mage::app();
        $this->subject = $this->getMockForAbstractClass(Subject::class);
    }

    /**
     * @group Mage_Rule
     * @group Mage_Rule_Model
     */
    public function testGetProductFlatSelect(): void
    {
        try {
            $this->assertInstanceOf(Varien_Db_Select::class, $this->subject->getProductFlatSelect(0));
        } catch (Mage_Core_Exception $exception) {
            $this->assertSame('Resource is not set.', $exception->getMessage());
        }
    }

    /**
     * @dataProvider provideBool
     * @group Mage_Rule
     * @group Mage_Rule_Model
     */
    public function testGetConditions(bool $empty): void
    {
        if (!$empty) {
            $this->subject->setConditions(new Mage_Rule_Model_Condition_Combine());
        }

        try {
            $this->assertInstanceOf(Mage_Rule_Model_Condition_Combine::class, $this->subject->getConditions());
        } catch (Error $error) {
            $this->assertSame(self::CALL_TO_A_MEMBER_FUNCTION_SET_RULE_ON_NULL, $error->getMessage());
        }
    }

    /**
     * @dataProvider provideBool
     * @group Mage_Rule
     * @group Mage_Rule_Model
     */
    public function testGetActions(bool $empty): void
    {
        if (!$empty) {
            $this->subject->setActions(new Mage_Rule_Model_Action_Collection());
        }

        try {
            $this->assertInstanceOf(Mage_Rule_Model_Action_Collection::class, $this->subject->getActions());
        } catch (Error $error) {
            $this->assertSame(self::CALL_TO_A_MEMBER_FUNCTION_SET_RULE_ON_NULL, $error->getMessage());
        }
    }

    /**
     * @group Mage_Rule
     * @group Mage_Rule_Model
     */
    public function testGetForm(): void
    {
        $this->assertInstanceOf(Varien_Data_Form::class, $this->subject->getForm());
    }

    /**
     * @group Mage_Rule
     * @group Mage_Rule_Model
     */
    public function testLoadPost(array $data = []): void
    {
        $this->assertInstanceOf(Subject::class, $this->subject->loadPost($data));
    }

    /**
     * @covers Mage_Rule_Model_Abstract::validate()
     * @dataProvider provideValidateData
     * @group Mage_Rule
     * @group Mage_Rule_Model
     */
    public function testValidate($expectedResul, ?array $data = null): void
    {
        $object = new Varien_Object($data);
        try {
            $this->assertSame($expectedResul, $this->subject->validate($object));
        } catch (Error $error) {
            $this->assertSame(self::CALL_TO_A_MEMBER_FUNCTION_SET_RULE_ON_NULL, $error->getMessage());
        }

    }

    /**
     * @dataProvider provideValidateData
     * @group Mage_Rule
     * @group Mage_Rule_Model
     */
    public function testValidateData($expectedResul, ?array $data = null): void
    {
        if (PHP_VERSION_ID >= 80300 && version_compare(InstalledVersions::getPrettyVersion('shardj/zf1-future'), '1.24.2', '<=')) {
            $this->markTestSkipped('see https://github.com/Shardj/zf1-future/pull/465');
        }
        $object = new Varien_Object($data);
        $this->assertSame($expectedResul, $this->subject->validateData($object));
    }

    /**
     * @covers Mage_Rule_Model_Abstract::isDeleteable()
     * @group Mage_Rule
     * @group Mage_Rule_Model
     */
    public function testIsDeleteable(): void
    {
        $this->assertIsBool($this->subject->isDeleteable());
    }

    /**
     * @covers Mage_Rule_Model_Abstract::setIsDeleteable()
     * @dataProvider provideBool
     * @group Mage_Rule
     * @group Mage_Rule_Model
     */
    public function testSetIsDeleteable(bool $value): void
    {
        $this->assertInstanceOf(Subject::class, $this->subject->setIsDeleteable($value));
    }

    /**
     * @covers Mage_Rule_Model_Abstract::isReadonly()
     * @group Mage_Rule
     * @group Mage_Rule_Model
     */
    public function testIsReadonly(): void
    {
        $this->assertIsBool($this->subject->isReadonly());
    }

    /**
     * @covers Mage_Rule_Model_Abstract::setIsReadonly()
     * @dataProvider provideBool
     * @group Mage_Rule
     * @group Mage_Rule_Model
     */
    public function testSetIsReadonly(bool $value): void
    {
        $this->assertInstanceOf(Subject::class, $this->subject->setIsReadonly($value));
    }

    /**
     * @group Mage_Rule
     * @group Mage_Rule_Model
     */
    public function testGetWebsiteIds(): void
    {
        try {
            $this->assertIsArray($this->subject->getWebsiteIds());
        } catch (Mage_Core_Exception $exception) {
            $this->assertSame('Resource is not set.', $exception->getMessage());
        }
    }

    /**
     * @covers Mage_Rule_Model_Abstract::asString()
     * @group Mage_Rule
     * @group Mage_Rule_Model
     */
    public function testAsString(): void
    {
        $this->assertSame('', $this->subject->asString());
    }

    /**
     * @covers Mage_Rule_Model_Abstract::asHtml()
     * @group Mage_Rule
     * @group Mage_Rule_Model
     */
    public function testAsHtml(): void
    {
        $this->assertSame('', $this->subject->asHtml());
    }

    /**
     * @covers Mage_Rule_Model_Abstract::asArray()
     * @group Mage_Rule
     * @group Mage_Rule_Model
     */
    public function testAsArray(): void
    {
        $this->assertSame([], $this->subject->asArray());
    }
}
