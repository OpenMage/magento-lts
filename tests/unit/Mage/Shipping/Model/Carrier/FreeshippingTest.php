<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Shipping\Model\Carrier;

# use Mage;
# use Mage_Shipping_Model_Carrier_Freeshipping as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Shipping\Model\Carrier\FreeshippingTrait;

final class FreeshippingTest extends OpenMageTest
{
    use FreeshippingTrait;

    # private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        # self::$subject = Mage::getModel('shipping/carrier_freeshipping');
        self::markTestSkipped('');
    }
}
