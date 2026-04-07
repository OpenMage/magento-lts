<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Bundle\Model\CatalogIndex\Data;

// use Mage;
// use Mage_Bundle_Model_CatalogIndex_Data_Bundle as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Bundle\Model\CatalogIndex\Data\BundleTrait;

final class BundleTest extends OpenMageTest
{
    use BundleTrait;

    // private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        // self::$subject = Mage::getModel('bundle/catalogindex_data_bundle');
        self::markTestSkipped('');
    }
}
