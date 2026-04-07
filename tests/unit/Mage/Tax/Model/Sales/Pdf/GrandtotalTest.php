<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Tax\Model\Sales\Pdf;

// use Mage;
// use Mage_Tax_Model_Sales_Pdf_Grandtotal as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Tax\Model\Sales\Pdf\GrandtotalTrait;

final class GrandtotalTest extends OpenMageTest
{
    use GrandtotalTrait;

    // private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        // self::$subject = Mage::getModel('tax/sales_pdf_grandtotal');
        self::markTestSkipped('');
    }
}
