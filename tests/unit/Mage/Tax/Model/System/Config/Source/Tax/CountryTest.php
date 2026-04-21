<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Tax\Model\System\Config\Source\Tax;

// use Mage;
// use Mage_Tax_Model_System_Config_Source_Tax_Country as Subject;
use Override;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Tax\Model\System\Config\Source\Tax\CountryTrait;

final class CountryTest extends OpenMageTest
{
    use CountryTrait;

    // private static Subject $subject;

    #[Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        // self::$subject = Mage::getModel('tax/system_config_source_tax_country');
        self::markTestSkipped('');
    }
}
