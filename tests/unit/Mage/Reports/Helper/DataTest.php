<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Reports\Helper;

use Composer\InstalledVersions;
use Mage;
use Mage_Reports_Helper_Data as Subject;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Reports\ReportsTrait;
use OpenMage\Tests\Unit\OpenMageTest;
use Varien_Data_Collection;
use Zend_Date_Exception;

final class DataTest extends OpenMageTest
{
    use ReportsTrait;

    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::helper('reports/data');
    }

    /**
     * @covers Mage_Core_Helper_Abstract::isModuleEnabled()
     * @group Helper
     */
    public function testIsModuleEnabled(): void
    {
        static::assertTrue(self::$subject->isModuleEnabled());
    }

    /**
     * @covers Mage_Reports_Helper_Data::isReportsEnabled()
     * @group Helper
     */
    public function testIsReportsEnabled(): void
    {
        static::assertTrue(self::$subject->isReportsEnabled());
    }

    /**
     * @covers Mage_Reports_Helper_Data::getIntervals()
     * @dataProvider provideReportsDateIntervals
     * @group Helper
     */
    public function testGetIntervals(int|string $expectedResult, string $from, string $to, string $period): void
    {
        if (PHP_VERSION_ID >= 80300 && version_compare(InstalledVersions::getPrettyVersion('shardj/zf1-future'), '1.24.2', '<=')) {
            static::markTestSkipped('see https://github.com/Shardj/zf1-future/pull/465');
        }

        try {
            static::assertCount($expectedResult, self::$subject->getIntervals($from, $to, $period));
        } catch (Zend_Date_Exception $exception) {
            static::assertSame("No date part in '' found.", $exception->getMessage());
        }
    }

    /**
     * @covers Mage_Reports_Helper_Data::prepareIntervalsCollection()
     * @dataProvider provideReportsDateIntervals
     * @group Helper
     */
    public function testPrepareIntervalsCollection(int|string $expectedResult, string $from, string $to, string $period): void
    {
        $collection = new Varien_Data_Collection();

        try {
            self::$subject->prepareIntervalsCollection($collection, $from, $to, $period);
            static::assertGreaterThanOrEqual(0, $collection->count());
        } catch (\Zend_Date_Exception $exception) {
            static::assertSame($expectedResult, $exception->getMessage());
        }
    }
}
