<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @group Mage_Page
 * @group Mage_Page_Block
 */
declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Page\Block;

use Mage;
use Mage_Page_Block_Redirect as Subject;
use PHPUnit\Framework\TestCase;

class RedirectTest extends TestCase
{
    public Subject $subject;

    public function setUp(): void
    {
        Mage::app();
        $this->subject = new Subject();
    }

    
    public function testGetTargetUrl(): void
    {
        $this->assertSame('', $this->subject->getTargetURL());
    }

    
    public function testGetMessage(): void
    {
        $this->assertSame('', $this->subject->getMessage());
    }

    
    public function testGetRedirectOutput(): void
    {
        $this->assertIsString($this->subject->getRedirectOutput());
    }

    
    public function testGetJsRedirect(): void
    {
        $this->assertIsString($this->subject->getJsRedirect());
    }

    
    public function testGetHtmlFormRedirect(): void
    {
        $this->assertIsString($this->subject->getHtmlFormRedirect());
    }

    
    public function testIsHtmlFormRedirect(): void
    {
        $this->assertIsBool($this->subject->isHtmlFormRedirect());
    }

    
    public function testGetFormId(): void
    {
        $this->assertSame('', $this->subject->getFormId());
    }

    
    public function testGetFormMethod(): void
    {
        $this->assertSame('POST', $this->subject->getFormMethod());
    }
}
