<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Sales\Model\Payment\Method\Billing;

// use Mage;
// use Mage_Sales_Model_Payment_Method_Billing_AgreementAbstract as Subject;
use Override;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Sales\Model\Payment\Method\Billing\AgreementAbstractTrait;

final class AgreementAbstractTest extends OpenMageTest
{
    use AgreementAbstractTrait;

    // private static Subject $subject;

    #[Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        // self::$subject = Mage::getModel('sales/payment_method_billing_agreementabstract');
        self::markTestSkipped('');
    }
}
