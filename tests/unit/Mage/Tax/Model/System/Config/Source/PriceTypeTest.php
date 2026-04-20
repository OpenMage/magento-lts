<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Tax\Model\System\Config\Source;

// use Mage;
// use Mage_Tax_Model_System_Config_Source_PriceType as Subject;
use Override;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Tax\Model\System\Config\Source\PriceTypeTrait;

final class PriceTypeTest extends OpenMageTest
{
    use PriceTypeTrait;

    // private static Subject $subject;

    #[Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        // self::$subject = Mage::getModel('tax/system_config_source_pricetype');
        self::markTestSkipped('');
    }
}
