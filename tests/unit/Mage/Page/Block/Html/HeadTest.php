<?php

/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @group Mage_Page
 * @group Mage_Page_Block
 */
declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Page\Block\Html;

use Mage;
use Mage_Page_Block_Html_Head as Subject;
use PHPUnit\Framework\TestCase;

class HeadTest extends TestCase
{
    public Subject $subject;

    public function setUp(): void
    {
        Mage::app();
        $this->subject = new Subject();
    }


    public function testAddCss(): void
    {
        $this->assertInstanceOf(Subject::class, $this->subject->addCss('test'));
    }


    public function testAddJs(): void
    {
        $this->assertInstanceOf(Subject::class, $this->subject->addJs('test'));
    }


    public function testAddCssIe(): void
    {
        $this->assertInstanceOf(Subject::class, $this->subject->addCssIe('test'));
    }


    public function testAddJsIe(): void
    {
        $this->assertInstanceOf(Subject::class, $this->subject->addJsIe('test'));
    }


    public function testAddLinkRel(): void
    {
        $this->assertInstanceOf(Subject::class, $this->subject->addLinkRel('test', 'ref'));
    }
}
