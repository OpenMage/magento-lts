<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @covers Mage_Sales_Block_Order_Item_Renderer_Default::setItem()
 * @group Mage_Sales
 * @group Mage_Sales_Block
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

    
    public function testSetItem(): void
    {
        $this->assertInstanceOf(Subject::class, $this->subject->setItem(new Varien_Object()));
    }
}
