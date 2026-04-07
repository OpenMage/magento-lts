<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Usa\Model\Shipping\Carrier\Dhl;

// use Mage;
// use Mage_Usa_Model_Shipping_Carrier_Dhl_International as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Usa\Model\Shipping\Carrier\Dhl\InternationalTrait;

final class InternationalTest extends OpenMageTest
{
    use InternationalTrait;

    // private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        // self::$subject = Mage::getModel('usa/shipping_carrier_dhl_international');
        self::markTestSkipped('');
    }
}
