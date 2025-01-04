<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   OpenMage
 * @package    OpenMage_Tests
 * @copyright  Copyright (c) 2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
