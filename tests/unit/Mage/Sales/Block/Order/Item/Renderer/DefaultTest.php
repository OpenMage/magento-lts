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
 * @copyright  Copyright (c) The OpenMage Contributors (https://openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Sales\Block\Order\Item\Renderer;

use Mage;
use Mage_Sales_Block_Order_Item_Renderer_Default as Subject;
use PHPUnit\Framework\TestCase;
use Varien_Object;

class DefaultTest extends TestCase
{
    public Subject $subject;

    public function setUp(): void
    {
        Mage::app();
        $this->subject = new Subject();
    }

    /**
     * @covers Mage_Sales_Block_Order_Item_Renderer_Default::setItem()
     * @group Mage_Sales
     * @group Mage_Sales_Block
     */
    public function testSetItem(): void
    {
        $this->assertInstanceOf(Subject::class, $this->subject->setItem(new Varien_Object()));
    }
}
