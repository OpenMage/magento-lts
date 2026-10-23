<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Fixture\Adminhtml\GridExport;

use Mage_Adminhtml_Block_Widget_Grid;
use Override;
use Varien_Object;

class ExportGrid extends Mage_Adminhtml_Block_Widget_Grid
{
    use ExportColumns;

    public function __construct(bool $withRows = true)
    {
        parent::__construct();
        $this->initColumns();
        $rows = $withRows ? [
            ['name' => 'Caffè "quoted" \\ path', 'amount' => '0'],
            ['name' => "Line one\r\nLine two", 'amount' => ' 42 '],
            ['name' => '=SUM(A1)', 'amount' => null],
        ] : [];
        $this->setCollection(new ExportCollection($rows));
        $this->setTotals(new Varien_Object(['name' => 'Total \\ "value"', 'amount' => '42']));
        $this->_exportPageSize = 2;
    }

    #[Override]
    protected function _beforeLoadCollection()
    {
        $this->calls[] = 'before';
        return $this;
    }

    #[Override]
    protected function _afterLoadCollection()
    {
        $this->calls[] = 'after';
        return $this;
    }
}
