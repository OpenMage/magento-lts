<?php

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Adminhtml\Block\Widget\Grid;

use Mage_Adminhtml_Block_Widget_Grid_Column;
use PHPUnit\Framework\TestCase;

class ColumnTest extends TestCase
{
    public Mage_Adminhtml_Block_Widget_Grid_Column $subject;

    public function setUp(): void
    {
        // phpcs:ignore Ecg.Classes.ObjectInstantiation.DirectInstantiation
        $this->subject = new Mage_Adminhtml_Block_Widget_Grid_Column();
    }

    /**
     * @group Mage_Adminhtml
     * @group Mage_Adminhtml_Block
     */
    public function testGetType(): void
    {
        $this->assertIsString($this->subject->getType());
    }
}
