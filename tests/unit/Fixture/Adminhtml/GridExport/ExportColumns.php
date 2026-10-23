<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Fixture\Adminhtml\GridExport;

use Mage_Adminhtml_Block_Widget_Grid_Column;
use Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Text;
use Override;

trait ExportColumns
{
    /** @var list<string> */
    public array $calls = [];

    protected function initColumns(bool $systemFirst = false): void
    {
        $this->_columns = [];
        foreach (array_filter([$systemFirst ? 'system' : null, 'name', !$systemFirst ? 'system' : null, 'amount']) as $index) {
            $column = new Mage_Adminhtml_Block_Widget_Grid_Column([
                'index' => $index,
                'header' => $index . ' "header"',
                'header_export' => $index . ' export',
                'is_system' => $index === 'system',
                'totals_label' => $index === 'name' ? 'All rows' : null,
            ]);
            $column->setRenderer((new Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Text())->setColumn($column));
            if ($index === 'name') {
                $column->setFrameCallback(static fn($value, $row, $column, $isExport) => ($isExport ? 'export:' : 'display:') . $value);
            }
            $this->_columns[$index] = $column;
        }
    }

    #[Override]
    protected function _prepareGrid()
    {
        $this->calls[] = 'prepare';
        return $this;
    }
}
