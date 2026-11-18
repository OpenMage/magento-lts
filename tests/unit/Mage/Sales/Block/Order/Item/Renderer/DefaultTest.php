<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Sales\Block\Order\Item\Renderer;

use Override;
use Mage_Sales_Block_Order_Item_Renderer_Default as Subject;
use Mage_Sales_Model_Order_Item;
use OpenMage\Tests\Unit\OpenMageTest;
use Varien_Object;

final class DefaultTest extends OpenMageTest
{
    private static Subject $subject;

    #[Override]
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
        self::assertInstanceOf(Subject::class, self::$subject->setItem(new Varien_Object()));
    }

    /**
     * Orders keep configurable option labels raw, while custom option values are stored as HTML
     *
     * @covers Mage_Sales_Block_Order_Item_Renderer_Default::getItemOptions()
     * @group Block
     */
    public function testGetItemOptionsEscapesConfigurableOptionLabels(): void
    {
        $orderItem = new Mage_Sales_Model_Order_Item();
        $orderItem->setProductOptions([
            'options' => [['label' => 'Engraving', 'value' => 'Fish &amp; Chips', 'option_id' => '5']],
            'attributes_info' => [['label' => 'Color', 'value' => '<img src=x onerror=alert(1)>']],
        ]);

        $subject = new Subject();
        $subject->setItem($orderItem);

        self::assertSame(
            [
                ['label' => 'Engraving', 'value' => 'Fish &amp; Chips', 'option_id' => '5'],
                ['label' => 'Color', 'value' => '&lt;img src=x onerror=alert(1)&gt;'],
            ],
            $subject->getItemOptions(),
        );
    }
}
