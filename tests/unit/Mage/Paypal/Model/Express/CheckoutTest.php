<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Paypal\Model\Express;

use Mage_Paypal_Model_Express_Checkout as Subject;
use Override;
use OpenMage\Tests\Unit\OpenMageTest;
use ReflectionMethod;
use Varien_Object;

final class CheckoutTest extends OpenMageTest
{
    private static Subject $subject;

    #[Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = new Subject();
    }

    public function testHasExportedBillingAddressDataIgnoresIdentityOnlyFields(): void
    {
        self::assertSame(
            false,
            $this->invokeHasExportedBillingAddressData(new Varien_Object([
                'exported_keys'  => ['email', 'firstname', 'lastname'],
                'customer_notes' => 'note',
                'email'          => 'customer@example.com',
                'firstname'      => 'Jane',
                'lastname'       => 'Doe',
            ])),
        );
    }

    public function testHasExportedBillingAddressDataDetectsAddressFieldsWithoutRegion(): void
    {
        self::assertSame(
            true,
            $this->invokeHasExportedBillingAddressData(new Varien_Object([
                'street'     => '1 Main St',
                'city'       => 'Austin',
                'postcode'   => '78701',
                'country_id' => 'US',
            ])),
        );
    }

    private function invokeHasExportedBillingAddressData(Varien_Object $exportedAddress): bool
    {
        $method = new ReflectionMethod(Subject::class, 'hasExportedBillingAddressData');
        return $method->invoke(self::$subject, $exportedAddress);
    }
}
