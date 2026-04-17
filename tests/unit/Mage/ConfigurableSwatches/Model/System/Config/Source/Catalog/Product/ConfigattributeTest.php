<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\ConfigurableSwatches\Model\System\Config\Source\Catalog\Product;

// use Mage;
// use Mage_ConfigurableSwatches_Model_System_Config_Source_Catalog_Product_Configattribute as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\ConfigurableSwatches\Model\System\Config\Source\Catalog\Product\ConfigattributeTrait;

final class ConfigattributeTest extends OpenMageTest
{
    use ConfigattributeTrait;

    // private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        // self::$subject = Mage::getModel('configurableswatches/system_config_source_catalog_product_configattribute');
        self::markTestSkipped('');
    }
}
