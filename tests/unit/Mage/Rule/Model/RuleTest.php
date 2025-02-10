<?php

/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @covers Mage_Rule_Model_Rule::getConditionsInstance()
 * @group Mage_Rule
 * @group Mage_Rule_Model
 */
declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Rule\Model;

use Mage;
use Mage_Rule_Model_Action_Collection;
use Mage_Rule_Model_Condition_Combine;
use Mage_Rule_Model_Rule as Subject;
use PHPUnit\Framework\TestCase;

class RuleTest extends TestCase
{
    public Subject $subject;

    public function setUp(): void
    {
        Mage::app();
        $this->subject = Mage::getModel('rule/rule');
    }


    public function testGetConditionsInstance(): void
    {
        $this->assertInstanceOf(Mage_Rule_Model_Condition_Combine::class, $this->subject->getConditionsInstance());
    }

    /**
     * @covers Mage_Rule_Model_Rule::getActionsInstance()
     * @group Mage_Rule
     * @group Mage_Rule_Model
     */
    public function testGetActionsInstance(): void
    {
        $this->assertInstanceOf(Mage_Rule_Model_Action_Collection::class, $this->subject->getActionsInstance());
    }
}
