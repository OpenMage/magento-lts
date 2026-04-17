<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Sales\Model\Order\Pdf;

// use Mage;
// use Mage_Sales_Model_Order_Pdf_Creditmemo as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Sales\Model\Order\Pdf\CreditmemoTrait;

final class CreditmemoTest extends OpenMageTest
{
    use CreditmemoTrait;

    // private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        // self::$subject = Mage::getModel('sales/order_pdf_creditmemo');
        self::markTestSkipped('');
    }
}
