<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Sales\Model\Order\Pdf\Items\Invoice;

use Mage;
use Mage_Sales_Model_Order_Pdf_Items_Invoice_Grouped as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Sales\Model\Order\Pdf\Items\Invoice\GroupedTrait;

final class GroupedTest extends OpenMageTest
{
    use GroupedTrait;

    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::getModel('sales/order_pdf_items_invoice_grouped');
        self::markTestSkipped('');
    }
}
