<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @group Mage_Adminhtml
 * @group Mage_Adminhtml_Block
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

    
    public function testGetType(): void
    {
        $this->assertSame('', $this->subject->getType());

        $this->subject->setType('text');
        $this->assertSame('text', $this->subject->getType());
    }
}
