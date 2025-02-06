<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   OpenMage
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Core\Helper;

use Mage;
use Mage_Core_Helper_Hint as Subject;
use PHPUnit\Framework\TestCase;

class HintTest extends TestCase
{
    public Subject $subject;

    public function setUp(): void
    {
        Mage::app();
        $this->subject = Mage::helper('core/hint');
    }

    /**
     * @covers Mage_Core_Helper_Hint::getAvailableHints()
     * @group Mage_Core
     * @group Mage_Core_Helper
     */
    public function testGetAvailableHints(): void
    {
        $this->assertSame([], $this->subject->getAvailableHints());
    }

    /**
     * @covers Mage_Core_Helper_Hint::getHintByCode()
     * @group Mage_Core
     * @group Mage_Core_Helper
     */
    public function testGetHintByCode(): void
    {
        $this->assertNull($this->subject->getHintByCode('test'));
    }
}
