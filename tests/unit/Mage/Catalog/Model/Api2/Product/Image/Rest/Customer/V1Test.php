<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Catalog\Model\Api2\Product\Image\Rest\Customer;

# use Mage;
# use Mage_Catalog_Model_Api2_Product_Image_Rest_Customer_V1 as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Catalog\Model\Api2\Product\Image\Rest\Customer\V1Trait;

final class V1Test extends OpenMageTest
{
    use V1Trait;

    # private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        # self::$subject = Mage::getModel('catalog/api2_product_image_rest_customer_v1');
        self::markTestSkipped('');
    }
}
