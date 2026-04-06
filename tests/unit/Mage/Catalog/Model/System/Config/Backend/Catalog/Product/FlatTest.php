<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Catalog\Model\System\Config\Backend\Catalog\Product;

use Mage;
use Mage_Catalog_Model_System_Config_Backend_Catalog_Product_Flat as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Catalog\Model\System\Config\Backend\Catalog\Product\FlatTrait;

final class FlatTest extends OpenMageTest
{
    use FlatTrait;

    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::getModel('catalog/system_config_backend_catalog_product_flat');
        self::markTestSkipped('');
    }
}
