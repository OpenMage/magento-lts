<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace unit\Mage\Catalog\Model;

use Mage;
use Mage_Catalog_Model_Layer as Subject;
use Mage_CatalogIndex_Model_Aggregation;
use OpenMage\Tests\Unit\OpenMageTest;

final class LayerTest extends OpenMageTest
{
    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::getModel('catalog/layer');
    }

    /**
     * @group Model
     */
    public function testGetAggregator(): void
    {
        self::assertInstanceOf(Mage_CatalogIndex_Model_Aggregation::class, self::$subject->getAggregator());
    }
}
