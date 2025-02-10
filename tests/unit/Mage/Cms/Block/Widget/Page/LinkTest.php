<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @group Mage_Cms
 * @group Mage_Cms_Block
 */
declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Cms\Block\Widget\Page;

use Mage;
use Mage_Cms_Block_Widget_Page_Link as Subject;
use PHPUnit\Framework\TestCase;

class LinkTest extends TestCase
{
    public Subject $subject;

    public function setUp(): void
    {
        Mage::app();
        $this->subject = new Subject();
    }

    
    public function testGetHref(): void
    {
        $this->assertIsString($this->subject->getHref());
    }

    
    public function testGetTitle(): void
    {
        $this->assertIsString($this->subject->getTitle());
    }

    
    //    public function testGetAnchorText(): void
    //    {
    //        $this->assertIsString($this->subject->getAnchorText());
    //    }
}
