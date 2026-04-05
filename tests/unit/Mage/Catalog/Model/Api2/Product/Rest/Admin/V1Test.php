<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Catalog\Model\Api2\Product\Rest\Admin;

use Mage;
use Mage_Catalog_Model_Api2_Product_Rest_Admin_V1 as Subject;
use OpenMage\Tests\Unit\OpenMageTest;

final class V1Test extends OpenMageTest
{
    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::getModel('catalog/api2_product_rest_admin_v1');
    }
}
