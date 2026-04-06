<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\ConfigurableSwatches\Model\System\Config\Source\Catalog\Product\Configattribute;

use Mage;
use Mage_ConfigurableSwatches_Model_System_Config_Source_Catalog_Product_Configattribute_Select as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\ConfigurableSwatches\Model\System\Config\Source\Catalog\Product\Configattribute\SelectTrait;

final class SelectTest extends OpenMageTest
{
    use SelectTrait;

    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::getModel('configurableswatches/system_config_source_catalog_product_configattribute_select');
    }
}
