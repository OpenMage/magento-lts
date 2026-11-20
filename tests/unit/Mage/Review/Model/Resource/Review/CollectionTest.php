<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Review\Model\Resource\Review;

use Mage;
use Mage_Core_Helper_Data;
use Mage_Review_Model_Resource_Review_Collection as Subject;
use PHPUnit\Framework\TestCase;
use Varien_Data_Collection;
use Varien_Object;

final class CollectionTest extends TestCase
{
    /**
     * @dataProvider provideRatingModuleStatus
     */
    public function testAddRateVotesWithOptionalRatingModule(bool $enabled): void
    {
        $originalHelper = Mage::registry('_helper/core');
        $helper = $this->createMock(Mage_Core_Helper_Data::class);
        $helper->expects(self::once())->method('isModuleEnabled')->with('Mage_Rating')->willReturn($enabled);

        $collection = $this->getMockBuilder(Subject::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getItems'])
            ->getMock();
        $review = new Varien_Object(['review_id' => 7]);
        $collection->expects(self::once())->method('getItems')->willReturn($enabled ? [] : [$review]);

        Mage::unregister('_helper/core');
        Mage::register('_helper/core', $helper);
        try {
            self::assertSame($collection, $collection->addRateVotes());
            if (!$enabled) {
                self::assertInstanceOf(Varien_Data_Collection::class, $review->getRatingVotes());
                self::assertCount(0, $review->getRatingVotes());
            }
        } finally {
            Mage::unregister('_helper/core');
            if ($originalHelper !== null) {
                Mage::register('_helper/core', $originalHelper);
            }
        }
    }

    public static function provideRatingModuleStatus(): array
    {
        return [
            'disabled' => [false],
            'enabled' => [true],
        ];
    }
}
