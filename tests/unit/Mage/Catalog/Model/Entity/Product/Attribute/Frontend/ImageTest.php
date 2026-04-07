<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Catalog\Model\Entity\Product\Attribute\Frontend;

// use Mage;
// use Mage_Catalog_Model_Entity_Product_Attribute_Frontend_Image as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Catalog\Model\Entity\Product\Attribute\Frontend\ImageTrait;

final class ImageTest extends OpenMageTest
{
    use ImageTrait;

    // private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        // self::$subject = Mage::getModel('catalog/entity_product_attribute_frontend_image');
        self::markTestSkipped('');
    }
}
