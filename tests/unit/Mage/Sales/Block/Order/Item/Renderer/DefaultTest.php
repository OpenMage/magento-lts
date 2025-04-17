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
use OpenMage\Tests\Unit\OpenMageTest;
use Varien_Object;

class DefaultTest extends OpenMageTest
{
    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = new Subject();
    }

    /**
     * @covers Mage_Sales_Block_Order_Item_Renderer_Default::setItem()
     * @group Block
     */
    public function testSetItem(): void
    {
        static::assertInstanceOf(self::$subject::class, self::$subject->setItem(new Varien_Object()));
    }
}
