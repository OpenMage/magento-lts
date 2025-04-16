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

use Mage;
use Mage_Rule_Model_Action_Collection;
use Mage_Rule_Model_Condition_Combine;
use Mage_Rule_Model_Rule as Subject;
use OpenMage\Tests\Unit\OpenMageTest;

class RuleTest extends OpenMageTest
{
    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::getModel('rule/rule');
    }

    /**
     * @covers Mage_Rule_Model_Rule::getConditionsInstance()
     * @group Model
     */
    public function testGetConditionsInstance(): void
    {
        static::assertInstanceOf(Mage_Rule_Model_Condition_Combine::class, self::$subject->getConditionsInstance());
    }

    /**
     * @covers Mage_Rule_Model_Rule::getActionsInstance()
     * @group Model
     */
    public function testGetActionsInstance(): void
    {
        static::assertInstanceOf(Mage_Rule_Model_Action_Collection::class, self::$subject->getActionsInstance());
    }
}
