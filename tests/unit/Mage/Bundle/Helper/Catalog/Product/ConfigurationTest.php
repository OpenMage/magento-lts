<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Bundle\Helper\Catalog\Product;

use Mage;
use Mage_Bundle_Helper_Catalog_Product_Configuration as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Bundle\Helper\Catalog\Product\ConfigurationTrait;

final class ConfigurationTest extends OpenMageTest
{
    use ConfigurationTrait;

    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::helper('bundle/catalog_product_configuration');
    }
}
