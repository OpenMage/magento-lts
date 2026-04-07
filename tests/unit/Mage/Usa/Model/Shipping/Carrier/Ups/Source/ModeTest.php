<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Usa\Model\Shipping\Carrier\Ups\Source;

// use Mage;
// use Mage_Usa_Model_Shipping_Carrier_Ups_Source_Mode as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Usa\Model\Shipping\Carrier\Ups\Source\ModeTrait;

final class ModeTest extends OpenMageTest
{
    use ModeTrait;

    // private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        // self::$subject = Mage::getModel('usa/shipping_carrier_ups_source_mode');
        self::markTestSkipped('');
    }
}
