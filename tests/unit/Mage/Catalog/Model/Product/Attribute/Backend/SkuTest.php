<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Catalog\Model\Product\Attribute\Backend;

// use Mage;
// use Mage_Catalog_Model_Product_Attribute_Backend_Sku as Subject;
use Override;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Catalog\Model\Product\Attribute\Backend\SkuTrait;

final class SkuTest extends OpenMageTest
{
    use SkuTrait;

    // private static Subject $subject;

    #[Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        // self::$subject = Mage::getModel('catalog/product_attribute_backend_sku');
        self::markTestSkipped('');
    }
}
