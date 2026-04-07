<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Paypal\Model\System\Config\Backend;

# use Mage;
use Mage_Paypal_Model_System_Config_Backend_MerchantCountry as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Paypal\Model\System\Config\Backend\MerchantCountryTrait;

final class MerchantCountryTest extends OpenMageTest
{
    use MerchantCountryTrait;

    # private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        # self::$subject = Mage::getModel('paypal/system_config_backend_merchantcountry');
        self::markTestSkipped('');
    }
}
