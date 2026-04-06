<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Catalog\Model\Resource\Product\Attribute\Frontend;

use Mage;
use Mage_Catalog_Model_Resource_Product_Attribute_Frontend_Tierprice as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Catalog\Model\Resource\Product\Attribute\Frontend\TierpriceTrait;

final class TierpriceTest extends OpenMageTest
{
    use TierpriceTrait;

    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::getModel('catalog/resource_product_attribute_frontend_tierprice');
        self::markTestSkipped('');
    }
}
