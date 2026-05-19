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
use Mage_Paypal_Model_Helper as Subject;
use Override;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Paypal\Model\HelperTrait;
use PaypalServerSdkLib\Models\Builders\MoneyBuilder;
use PaypalServerSdkLib\Models\Builders\OrderBuilder;
use PaypalServerSdkLib\Models\Builders\OrdersCaptureBuilder;
use PaypalServerSdkLib\Models\Builders\PaymentCollectionBuilder;
use PaypalServerSdkLib\Models\Builders\PurchaseUnitBuilder;
use Varien_Object;

final class HelperTest extends OpenMageTest
{
    use HelperTrait;

    private static Subject $subject;

    #[Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::getModel('paypal/helper');
    }

    /**
     * Build a PayPal order API result with the requested shape.
     */
    private function buildResult(string $kind, ?string $amount): mixed
    {
        if ($kind === 'null') {
            return null;
        }
        if ($kind === 'string') {
            return 'not-an-object';
        }
        if ($kind === 'plain') {
            return new Varien_Object();
        }
        if ($kind === 'emptyUnits') {
            return OrderBuilder::init()->purchaseUnits([])->build();
        }
        if ($kind === 'noPayments') {
            return OrderBuilder::init()->purchaseUnits([PurchaseUnitBuilder::init()->build()])->build();
        }

        $captures = [];
        if ($kind !== 'emptyCaptures') {
            $capture = OrdersCaptureBuilder::init()->id('CAP-1')->status('COMPLETED');
            if ($amount !== null) {
                $capture->amount(MoneyBuilder::init('USD', $amount)->build());
            }

            $captures[] = $capture->build();
        }

        $purchaseUnit = PurchaseUnitBuilder::init()
            ->payments(PaymentCollectionBuilder::init()->captures($captures)->build())
            ->build();

        return OrderBuilder::init()->purchaseUnits([$purchaseUnit])->build();
    }

    /**
     * @dataProvider provideCaptureResultShapes
     * @group Model
     */
    public function testExtractCaptureAmount(string $kind, ?string $amount): void
    {
        $result = $this->buildResult($kind, $amount);
        $extracted = self::$subject->extractCaptureAmount($result);

        if ($kind === 'ok') {
            self::assertSame((float) $amount, $extracted);
        } else {
            self::assertNull($extracted);
        }
    }

    /**
     * @dataProvider provideCaptureResultShapes
     * @group Model
     */
    public function testExtractCaptureId(string $kind, ?string $amount): void
    {
        $result = $this->buildResult($kind, $amount);
        $extracted = self::$subject->extractCaptureId($result);

        if ($kind === 'ok' || $kind === 'noAmount') {
            // Both shapes carry a capture with an ID.
            self::assertSame('CAP-1', $extracted);
        } else {
            self::assertNull($extracted);
        }
    }
}
