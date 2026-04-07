<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\SalesRule\Model\System\Config\Source\Coupon;

# use Mage;
# use Mage_SalesRule_Model_System_Config_Source_Coupon_Format as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\SalesRule\Model\System\Config\Source\Coupon\FormatTrait;

final class FormatTest extends OpenMageTest
{
    use FormatTrait;

    # private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        # self::$subject = Mage::getModel('salesrule/system_config_source_coupon_format');
        self::markTestSkipped('');
    }
}
