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
use Mage_Core_Model_Security_HtmlEscapedString;
use Mage_Page_Block_Html_Header as Subject;
use PHPUnit\Framework\TestCase;

class HeaderTest extends TestCase
{
    public Subject $subject;

    public function setUp(): void
    {
        Mage::app();
        $this->subject = new Subject();
    }

    
    //    public function testGetIsHomePage(): void
    //    {
    //        $this->assertIsBool($this->subject->getIsHomePage());
    //    }

    
    public function testSetLogo(): void
    {
        $this->assertInstanceOf(Subject::class, $this->subject->setLogo('src', 'alt'));
    }

    
    public function testGetLogoSrc(): void
    {
        $this->assertIsString($this->subject->getLogoSrc());
    }

    
    public function testGetLogoSrcSmall(): void
    {
        $this->assertIsString($this->subject->getLogoSrcSmall());
    }

    
    public function testGetLogoAlt(): void
    {
        $this->assertInstanceOf(Mage_Core_Model_Security_HtmlEscapedString::class, $this->subject->getLogoAlt());
    }
}
