<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Paypal\Model\System\Config\Source;

// use Mage;
// use Mage_Paypal_Model_System_Config_Source_BuyerCountry as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Paypal\Model\System\Config\Source\BuyerCountryTrait;

final class BuyerCountryTest extends OpenMageTest
{
    use BuyerCountryTrait;

    // private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        // self::$subject = Mage::getModel('paypal/system_config_source_buyercountry');
        self::markTestSkipped('');
    }
}
