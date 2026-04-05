<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\GiftMessage\Block\Adminhtml\Sales\Order\View;

use Mage_GiftMessage_Block_Adminhtml_Sales_Order_View_Items as Subject;
use OpenMage\Tests\Unit\OpenMageTest;

final class ItemsTest extends OpenMageTest
{
    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = new Subject();
    }
}
