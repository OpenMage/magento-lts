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
use OpenMage\Tests\Unit\OpenMageTest;

class HeaderTest extends OpenMageTest
{
    private static Subject $subject;

    public function setUp(): void
    {
        self::$subject = new Subject();
    }

    /**
     * @group Block
     */
    //    public function testGetIsHomePage(): void
    //    {
    //        $this->assertIsBool(self::$subject->getIsHomePage());
    //    }

    /**
     * @group Block
     */
    public function testSetLogo(): void
    {
        static::assertInstanceOf(self::$subject::class, self::$subject->setLogo('src', 'alt'));
    }

    /**
     * @group Block
     */
    public function testGetLogoSrc(): void
    {
        static::assertIsString(self::$subject->getLogoSrc());
    }

    /**
     * @group Block
     */
    public function testGetLogoSrcSmall(): void
    {
        static::assertIsString(self::$subject->getLogoSrcSmall());
    }

    /**
     * @group Block
     */
    public function testGetLogoAlt(): void
    {
        static::assertInstanceOf(Mage_Core_Model_Security_HtmlEscapedString::class, self::$subject->getLogoAlt());
    }
}
