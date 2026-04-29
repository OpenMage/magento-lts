<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Usa\Model\Shipping\Carrier\Usps\Address;

use Mage;
use Mage_Usa_Model_Shipping_Carrier_Usps_Address_Service as Subject;
use Override;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Usa\Model\Shipping\Carrier\Usps\Address\ServiceTrait;

final class ServiceTest extends OpenMageTest
{
    use ServiceTrait;

    private static Subject $subject;

    #[Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::getModel('usa/shipping_carrier_usps_address_service');
    }

    /**
     * @covers \Mage_Usa_Model_Shipping_Carrier_Usps_Address_Service::isEnabled()
     * @group Model
     */
    public function testIsEnabled(): void
    {
        self::$subject->isEnabled();
        self::markTestSkipped('');
    }
}
