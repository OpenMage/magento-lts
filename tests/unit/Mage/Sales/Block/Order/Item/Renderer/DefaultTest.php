<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Sales\Block\Order\Item\Renderer;

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
        static::assertInstanceOf(Subject::class, self::$subject->setItem(new Varien_Object()));
    }
}
