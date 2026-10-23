<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Tax\Model\Sales\Total\Quote\Nominal;

// use Mage;
// use Mage_Tax_Model_Sales_Total_Quote_Nominal_Tax as Subject;
use Override;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Tax\Model\Sales\Total\Quote\Nominal\TaxTrait;

final class TaxTest extends OpenMageTest
{
    use TaxTrait;

    // private static Subject $subject;

    #[Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        // self::$subject = Mage::getModel('tax/sales_total_quote_nominal_tax');
        self::markTestSkipped('');
    }
}
