<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   OpenMage
 * @package    OpenMage_Tests
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

    /**
     * @group Mage_Page
     * @group Mage_Page_Block
     */
    //    public function testGetIsHomePage(): void
    //    {
    //        $this->assertIsBool($this->subject->getIsHomePage());
    //    }

    /**
     * @group Mage_Page
     * @group Mage_Page_Block
     */
    public function testSetLogo(): void
    {
        $this->assertInstanceOf(Subject::class, $this->subject->setLogo('src', 'alt'));
    }

    /**
     * @group Mage_Page
     * @group Mage_Page_Block
     */
    public function testGetLogoSrc(): void
    {
        $this->assertIsString($this->subject->getLogoSrc());
    }

    /**
     * @group Mage_Page
     * @group Mage_Page_Block
     */
    public function testGetLogoSrcSmall(): void
    {
        $this->assertIsString($this->subject->getLogoSrcSmall());
    }

    /**
     * @group Mage_Page
     * @group Mage_Page_Block
     */
    public function testGetLogoAlt(): void
    {
        $this->assertInstanceOf(Mage_Core_Model_Security_HtmlEscapedString::class, $this->subject->getLogoAlt());
    }
}
