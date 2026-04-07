<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\CatalogIndex\Model\Resource\Data;

# use Mage;
# use Mage_CatalogIndex_Model_Resource_Data_Configurable as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\CatalogIndex\Model\Resource\Data\ConfigurableTrait;

final class ConfigurableTest extends OpenMageTest
{
    use ConfigurableTrait;

    # private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        # self::$subject = Mage::getModel('catalogindex/resource_data_configurable');
        self::markTestSkipped('');
    }
}
