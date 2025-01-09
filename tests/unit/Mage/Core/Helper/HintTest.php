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
