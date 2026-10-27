<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Paypal\Model;

use Iterator;
use Mage;
use Mage_Paypal_Block_Hosted_Pro_Info;
use Mage_Paypal_Block_Payflow_Advanced_Info;
use Mage_Paypal_Block_Payflow_Link_Info;
use Mage_Paypal_Block_Payment_Info;
use Mage_Paypal_Model_Legacy_Abstract;
use Mage_Sales_Block_Payment_Info_Billing_Agreement;
use OpenMage\Tests\Unit\OpenMageTest;

final class LegacyTest extends OpenMageTest
{
    /**
     * @dataProvider provideLegacyMethodCodes
     * @group Model
     */
    public function testLegacyPaymentMethodAliasesRemainLoadable(string $methodCode, string $infoBlockType): void
    {
        $method = Mage::helper('payment')->getMethodInstance($methodCode);

        self::assertInstanceOf(Mage_Paypal_Model_Legacy_Abstract::class, $method);
        self::assertSame($methodCode, $method->getCode());
        self::assertSame($infoBlockType, $method->getInfoBlockType());
        self::assertFalse($method->isAvailable());
        self::assertFalse($method->canUseCheckout());
        self::assertFalse($method->canUseInternal());
    }

    /**
     * @dataProvider provideLegacyInfoBlockAliases
     * @param class-string $blockClass
     * @group Block
     */
    public function testLegacyInfoBlockAliasesRemainLoadable(string $blockType, string $blockClass): void
    {
        $block = Mage::app()->getLayout()->createBlock($blockType);

        self::assertInstanceOf($blockClass, $block);
    }

    /**
     * @group Block
     */
    public function testLegacyPaymentInfoBlockKeepsLegacySpecificInformation(): void
    {
        $payment = Mage::getModel('sales/order_payment');
        $payment->setMethod('paypal_express')
            ->setLastTransId('PAYPAL-TXN-1')
            ->setAdditionalInformation([
                'paypal_payer_email' => 'payer@example.com',
                'paypal_payer_id' => 'PAYER-1',
                'paypal_fraud_filters' => ['Filter One', 'Filter Two'],
                'paypal_avs_code' => 'Y',
            ]);

        $block = Mage::app()->getLayout()->createBlock('paypal/payment_info');
        self::assertInstanceOf(Mage_Paypal_Block_Payment_Info::class, $block);

        $block->setInfo($payment)->setIsSecureMode(false);
        $specificInfo = $block->getSpecificInformation();

        self::assertSame('payer@example.com', $specificInfo['Payer Email']);
        self::assertSame('PAYER-1', $specificInfo['Payer ID']);
        self::assertSame(['Filter One', 'Filter Two'], $specificInfo['Triggered Fraud Filters']);
        self::assertSame('PAYPAL-TXN-1', $specificInfo['Last Transaction ID']);
        self::assertSame('#Y: Yes. Matched Address and five-didgit ZIP', $specificInfo['Address Verification System Response']);
    }

    /**
     * @return Iterator<string, array{string, string}>
     */
    public static function provideLegacyMethodCodes(): Iterator
    {
        yield 'paypal express' => ['paypal_express', 'paypal/payment_info'];
        yield 'paypal credit' => ['paypal_express_bml', 'paypal/payment_info'];
        yield 'paypal direct' => ['paypal_direct', 'paypal/payment_info'];
        yield 'paypal standard' => ['paypal_standard', 'paypal/payment_info'];
        yield 'paypal uk express' => ['paypaluk_express', 'paypal/payment_info'];
        yield 'paypal uk credit' => ['paypaluk_express_bml', 'paypal/payment_info'];
        yield 'paypal uk direct' => ['paypaluk_direct', 'paypal/payment_info'];
        yield 'verisign payflow pro' => ['verisign', 'payment/info_cc'];
        yield 'billing agreement' => ['paypal_billing_agreement', 'sales/payment_info_billing_agreement'];
        yield 'payflow link' => ['payflow_link', 'paypal/payflow_link_info'];
        yield 'payflow advanced' => ['payflow_advanced', 'paypal/payflow_advanced_info'];
        yield 'hosted pro' => ['hosted_pro', 'paypal/hosted_pro_info'];
        yield 'wps express' => ['paypal_wps_express', 'paypal/payment_info'];
    }

    /**
     * @return Iterator<string, array{string, class-string}>
     */
    public static function provideLegacyInfoBlockAliases(): Iterator
    {
        yield 'payment info' => ['paypal/payment_info', Mage_Paypal_Block_Payment_Info::class];
        yield 'hosted pro info' => ['paypal/hosted_pro_info', Mage_Paypal_Block_Hosted_Pro_Info::class];
        yield 'payflow link info' => ['paypal/payflow_link_info', Mage_Paypal_Block_Payflow_Link_Info::class];
        yield 'payflow advanced info' => ['paypal/payflow_advanced_info', Mage_Paypal_Block_Payflow_Advanced_Info::class];
        yield 'billing agreement info' => [
            'sales/payment_info_billing_agreement',
            Mage_Sales_Block_Payment_Info_Billing_Agreement::class,
        ];
    }
}
