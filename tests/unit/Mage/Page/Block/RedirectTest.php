<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
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

    /**
     * @group Mage_Page
     * @group Mage_Page_Block
     */
    public function testGetTargetUrl(): void
    {
        $this->assertSame('', $this->subject->getTargetURL());
    }

    /**
     * @group Mage_Page
     * @group Mage_Page_Block
     */
    public function testGetMessage(): void
    {
        $this->assertSame('', $this->subject->getMessage());
    }

    /**
     * @group Mage_Page
     * @group Mage_Page_Block
     */
    public function testGetRedirectOutput(): void
    {
        $this->assertIsString($this->subject->getRedirectOutput());
    }

    /**
     * @group Mage_Page
     * @group Mage_Page_Block
     */
    public function testGetJsRedirect(): void
    {
        $this->assertIsString($this->subject->getJsRedirect());
    }

    /**
     * @group Mage_Page
     * @group Mage_Page_Block
     */
    public function testGetHtmlFormRedirect(): void
    {
        $this->assertIsString($this->subject->getHtmlFormRedirect());
    }

    /**
     * @group Mage_Page
     * @group Mage_Page_Block
     */
    public function testIsHtmlFormRedirect(): void
    {
        $this->assertIsBool($this->subject->isHtmlFormRedirect());
    }

    /**
     * @group Mage_Page
     * @group Mage_Page_Block
     */
    public function testGetFormId(): void
    {
        $this->assertSame('', $this->subject->getFormId());
    }

    /**
     * @group Mage_Page
     * @group Mage_Page_Block
     */
    public function testGetFormMethod(): void
    {
        $this->assertSame('POST', $this->subject->getFormMethod());
    }
}
