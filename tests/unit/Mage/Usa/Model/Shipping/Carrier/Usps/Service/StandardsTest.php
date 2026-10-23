<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Usa\Model\Shipping\Carrier\Usps\Service;

use Mage;
use Mage_Usa_Model_Shipping_Carrier_Usps_Service_Standards as Subject;
use Override;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Usa\Model\Shipping\Carrier\Usps\Service\StandardsTrait;

final class StandardsTest extends OpenMageTest
{
    use StandardsTrait;

    private static Subject $subject;

    #[Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::getModel('usa/shipping_carrier_usps_service_standards');
    }

    /**
     * @covers \Mage_Usa_Model_Shipping_Carrier_Usps_Service_Standards::isEnabled()
     * @group Model
     */
    public function testIsEnabled(): void
    {
        self::$subject->isEnabled();
        self::markTestSkipped('');
    }
}
