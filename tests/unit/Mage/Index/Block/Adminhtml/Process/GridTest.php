<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   OpenMage
 * @package    OpenMage_Tests
 * @copyright  Copyright (c) The OpenMage Contributors (https://openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Index\Block\Adminhtml\Process;

use Mage_Adminhtml_Block_Widget_Grid_Column;
use Mage_Index_Block_Adminhtml_Process_Grid as Subject;
use Mage_Index_Model_Process;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Index\Block\Adminhtml\Process\GridTrait;

class GridTest extends OpenMageTest
{
    use GridTrait;

    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = new Subject();
    }

    /**
     * @covers Mage_Index_Block_Adminhtml_Process_Grid::decorateStatus()
     * @dataProvider provideDecorateStatusData
     * @group Model
     */
    public function testDecorateStatus(string $expectedResult, string $data): void
    {
        $value      = '1';
        $row        = new Mage_Index_Model_Process();
        $column     = new Mage_Adminhtml_Block_Widget_Grid_Column();
        $isExpected = false;

        $row->setStatus($data);

        $expectedResult = sprintf(self::$subject::PATTERN_SEVERITY, $expectedResult, $value);
        static::assertSame($expectedResult, self::$subject->decorateStatus($value, $row, $column, $isExpected));
    }

    /**
     * @covers Mage_Index_Block_Adminhtml_Process_Grid::decorateUpdateRequired()
     * @dataProvider provideDecorateUpdateRequiredData
     * @group Model
     */
    public function testDecorateUpdateRequired(string $expectedResult, int $data): void
    {
        $value      = '1';
        $row        = new Mage_Index_Model_Process();
        $column     = new Mage_Adminhtml_Block_Widget_Grid_Column();
        $isExpected = false;

        $row->setUpdateRequired($data);

        $expectedResult = sprintf(self::$subject::PATTERN_SEVERITY, $expectedResult, $value);
        static::assertSame($expectedResult, self::$subject->decorateUpdateRequired($value, $row, $column, $isExpected));
    }
}
