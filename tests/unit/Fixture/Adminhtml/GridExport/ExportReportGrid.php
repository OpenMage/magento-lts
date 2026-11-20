<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Fixture\Adminhtml\GridExport;

use Mage_Adminhtml_Block_Report_Grid;
use Mage_Reports_Model_Resource_Report_Collection;
use Override;
use Varien_Object;

/**
 * Exercise the real Magento grid hierarchy without changing production inheritance.
 *
 * @SuppressWarnings("php:S110")
 */
class ExportReportGrid extends Mage_Adminhtml_Block_Report_Grid
{
    use ExportColumns;

    public function __construct(bool $withRows = true, bool $systemFirst = false)
    {
        parent::__construct();
        $this->initColumns($systemFirst);
        $collection = new class ($withRows) extends Mage_Reports_Model_Resource_Report_Collection {
            public function __construct(bool $withRows)
            {
                $this->_intervals = $withRows ? [
                    'period "one"' => ['start' => 'first', 'end' => 'first-end'],
                    'period two' => ['start' => 'second', 'end' => 'second-end'],
                ] : [];
            }
        };
        $this->setCollection($collection);
        $this->_grandTotals = new Varien_Object(['name' => 'Grand \\ "total"', 'amount' => '42']);
    }

    /**
     * @return list<Varien_Object>
     */
    #[Override]
    public function getReport(mixed $dateFrom, mixed $dateTo): array
    {
        $this->calls[] = (string) $dateFrom;
        $this->setTotals(new Varien_Object(['name' => 'Sub \\ "total"', 'amount' => '21']));
        return $dateFrom === 'first' ? [new Varien_Object(['name' => 'Item \\ "value"', 'amount' => '21'])] : [];
    }
}
