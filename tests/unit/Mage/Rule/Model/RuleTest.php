<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Rule\Model;

use Mage;
use Mage_Rule_Model_Action_Collection;
use Mage_Rule_Model_Condition_Combine;
use Mage_Rule_Model_Rule as Subject;
use OpenMage\Tests\Unit\OpenMageTest;

final class RuleTest extends OpenMageTest
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
        self::assertInstanceOf(Mage_Rule_Model_Condition_Combine::class, self::$subject->getConditionsInstance());
    }

    /**
     * @covers Mage_Rule_Model_Rule::getActionsInstance()
     * @group Model
     */
    public function testGetActionsInstance(): void
    {
        self::assertInstanceOf(Mage_Rule_Model_Action_Collection::class, self::$subject->getActionsInstance());
    }
}
