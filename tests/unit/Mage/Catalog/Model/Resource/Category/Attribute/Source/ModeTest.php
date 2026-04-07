<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Catalog\Model\Resource\Category\Attribute\Source;

# use Mage;
use Mage_Catalog_Model_Resource_Category_Attribute_Source_Mode as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Catalog\Model\Resource\Category\Attribute\Source\ModeTrait;

final class ModeTest extends OpenMageTest
{
    use ModeTrait;

    # private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        # self::$subject = Mage::getModel('catalog/resource_category_attribute_source_mode');
        self::markTestSkipped('');
    }
}
