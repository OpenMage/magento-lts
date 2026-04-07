<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Sales\Model\Quote\Address\Total\Nominal\Recurring;

# use Mage;
use Mage_Sales_Model_Quote_Address_Total_Nominal_Recurring_Initial as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Sales\Model\Quote\Address\Total\Nominal\Recurring\InitialTrait;

final class InitialTest extends OpenMageTest
{
    use InitialTrait;

    # private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        # self::$subject = Mage::getModel('sales/quote_address_total_nominal_recurring_initial');
        self::markTestSkipped('');
    }
}
