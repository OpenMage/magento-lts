<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Paypal\Model;

use Mage;
use Mage_Paypal_Model_Legacy_Abstract;
use OpenMage\Tests\Unit\OpenMageTest;

final class LegacyTest extends OpenMageTest
{
    /**
     * @dataProvider provideLegacyMethodCodes
     * @group Model
     */
    public function testLegacyPaymentMethodAliasesRemainLoadable(string $methodCode): void
    {
        $method = Mage::helper('payment')->getMethodInstance($methodCode);

        self::assertInstanceOf(Mage_Paypal_Model_Legacy_Abstract::class, $method);
        self::assertSame($methodCode, $method->getCode());
        self::assertFalse($method->isAvailable());
        self::assertFalse($method->canUseCheckout());
        self::assertFalse($method->canUseInternal());
    }

    /**
     * @return array<string, array{string}>
     */
    public static function provideLegacyMethodCodes(): array
    {
        return [
            'paypal express' => ['paypal_express'],
            'paypal credit' => ['paypal_express_bml'],
            'paypal direct' => ['paypal_direct'],
            'paypal standard' => ['paypal_standard'],
            'paypal uk express' => ['paypaluk_express'],
            'paypal uk credit' => ['paypaluk_express_bml'],
            'paypal uk direct' => ['paypaluk_direct'],
            'verisign payflow pro' => ['verisign'],
            'billing agreement' => ['paypal_billing_agreement'],
            'payflow link' => ['payflow_link'],
            'payflow advanced' => ['payflow_advanced'],
            'hosted pro' => ['hosted_pro'],
            'wps express' => ['paypal_wps_express'],
        ];
    }
}
