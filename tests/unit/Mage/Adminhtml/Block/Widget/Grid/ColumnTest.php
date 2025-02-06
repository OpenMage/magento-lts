<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   OpenMage
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Adminhtml\Block\Widget\Grid;

use Mage_Adminhtml_Block_Widget_Grid_Column as Subject;
use PHPUnit\Framework\TestCase;

class ColumnTest extends TestCase
{
    public Subject $subject;

    public function setUp(): void
    {
        $this->subject = new Subject();
    }

    /**
     * @group Mage_Adminhtml
     * @group Mage_Adminhtml_Block
     */
    public function testGetType(): void
    {
        $this->assertSame('', $this->subject->getType());

        $this->subject->setType('text');
        $this->assertSame('text', $this->subject->getType());
    }
}
