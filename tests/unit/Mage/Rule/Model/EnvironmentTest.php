<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Rule\Model;

use Mage;
use Mage_Rule_Model_Environment as Subject;
use PHPUnit\Framework\TestCase;

class EnvironmentTest extends TestCase
{
    public Subject $subject;

    public function setUp(): void
    {
        Mage::app();
        $this->subject = Mage::getModel('rule/environment');
    }

    /**
     * @covers Mage_Rule_Model_Environment::collect()
     * @group Mage_Rule
     * @group Mage_Rule_Model
     */
    public function testGetConditionsInstance(): void
    {
        $this->assertInstanceOf(Subject::class, $this->subject->collect());
    }
}
