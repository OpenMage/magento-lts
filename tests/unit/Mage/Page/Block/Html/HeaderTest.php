<?php

/**
 * @category   OpenMage
 * @package    OpenMage_Tests
 * @copyright  Copyright (c) The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Page\Block\Html;

use Mage;
use Mage_Core_Model_Security_HtmlEscapedString;
use Mage_Page_Block_Html_Header;
use PHPUnit\Framework\TestCase;

class HeaderTest extends TestCase
{
    public Mage_Page_Block_Html_Header $subject;

    public function setUp(): void
    {
        Mage::app();
        // phpcs:ignore Ecg.Classes.ObjectInstantiation.DirectInstantiation
        $this->subject = new Mage_Page_Block_Html_Header();
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
        $this->assertInstanceOf(Mage_Page_Block_Html_Header::class, $this->subject->setLogo('src', 'alt'));
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
