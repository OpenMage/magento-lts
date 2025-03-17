<?php

/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @covers Mage_Core_Helper_Abstract::isModuleEnabled()
 * @group Mage_Reports
 * @group Mage_Reports_Helper
 */
declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Reports\Helper;

use Composer\InstalledVersions;
use Mage;
use Mage_Reports_Helper_Data as Subject;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Reports\ReportsTrait;
use PHPUnit\Framework\TestCase;
use Varien_Data_Collection;
use Zend_Date_Exception;

class DataTest extends TestCase
{
    use ReportsTrait;

    public Subject $subject;

    public function setUp(): void
    {
        Mage::app();
        $this->subject = Mage::helper('reports/data');
    }


    public function testIsModuleEnabled(): void
    {
        $this->assertTrue($this->subject->isModuleEnabled());
    }

    /**
     * @covers Mage_Reports_Helper_Data::isReportsEnabled()
     * @group Mage_Reports
     * @group Mage_Reports_Helper
     */
    public function testIsReportsEnabled(): void
    {
        $this->assertTrue($this->subject->isReportsEnabled());
    }

    /**
     * @covers Mage_Reports_Helper_Data::getIntervals()
     * @dataProvider provideReportsDateIntervals
     * @group Mage_Reports
     * @group Mage_Reports_Helper
     */
    public function testGetIntervals($expectedResult, $from, $to, $period): void
    {
        if (PHP_VERSION_ID >= 80300 && version_compare(InstalledVersions::getPrettyVersion('shardj/zf1-future'), '1.24.2', '<=')) {
            $this->markTestSkipped('see https://github.com/Shardj/zf1-future/pull/465');
        }

        try {
            $this->assertCount($expectedResult, $this->subject->getIntervals($from, $to, $period));
        } catch (Zend_Date_Exception $exception) {
            $this->assertSame("No date part in '' found.", $exception->getMessage());
        }
    }

    /**
     * @covers Mage_Reports_Helper_Data::prepareIntervalsCollection()
     * @dataProvider provideReportsDateIntervals
     * @doesNotPerformAssertions
     * @group Mage_Reports
     * @group Mage_Reports_Helper
     */
    public function testPrepareIntervalsCollection($expectedResult, $from, $to, $period): void
    {
        $this->markTestIncomplete('Test needs to be reviewed.');
        // @phpstan-ignore-next-line
        $this->subject->prepareIntervalsCollection(new Varien_Data_Collection(), $from, $to, $period);
    }
}
