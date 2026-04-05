<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Downloadable\Model\Sales\Order\Pdf\Items;

use Mage;
use Mage_Downloadable_Model_Sales_Order_Pdf_Items_Invoice as Subject;
use OpenMage\Tests\Unit\OpenMageTest;

final class InvoiceTest extends OpenMageTest
{
    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::getModel('downloadable/sales_order_pdf_items_invoice');
    }
}
