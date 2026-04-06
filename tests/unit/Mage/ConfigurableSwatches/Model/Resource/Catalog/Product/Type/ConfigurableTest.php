<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\ConfigurableSwatches\Model\Resource\Catalog\Product\Type;

use Mage;
use Mage_ConfigurableSwatches_Model_Resource_Catalog_Product_Type_Configurable as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\ConfigurableSwatches\Model\Resource\Catalog\Product\Type\ConfigurableTrait;

final class ConfigurableTest extends OpenMageTest
{
    use ConfigurableTrait;

    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::getModel('configurableswatches/resource_catalog_product_type_configurable');
        self::markTestSkipped('');
    }
}
