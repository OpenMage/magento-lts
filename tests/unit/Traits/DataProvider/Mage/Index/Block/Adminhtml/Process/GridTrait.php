<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Traits\DataProvider\Mage\Index\Block\Adminhtml\Process;

use Generator;
use Mage_Adminhtml_Block_Widget_Grid;
use Mage_Index_Model_Process;

trait GridTrait
{
    public function provideDecorateStatusData(): Generator
    {
        yield 'pending' => [
            Mage_Adminhtml_Block_Widget_Grid::CSS_SEVERITY_NOTICE,
            Mage_Index_Model_Process::STATUS_PENDING,
        ];

        yield 'working' => [
            Mage_Adminhtml_Block_Widget_Grid::CSS_SEVERITY_MAJOR,
            Mage_Index_Model_Process::STATUS_RUNNING,
        ];

        yield 'require reindex' => [
            Mage_Adminhtml_Block_Widget_Grid::CSS_SEVERITY_CRITICAL,
            Mage_Index_Model_Process::STATUS_REQUIRE_REINDEX,
        ];
    }

    public function provideDecorateUpdateRequiredData(): Generator
    {
        yield 'no' => [
            Mage_Adminhtml_Block_Widget_Grid::CSS_SEVERITY_NOTICE,
            0,
        ];

        yield 'yes' => [
            Mage_Adminhtml_Block_Widget_Grid::CSS_SEVERITY_CRITICAL,
            1,
        ];
    }
}
