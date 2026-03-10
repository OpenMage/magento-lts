<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Page\Block\Html;

use Mage;
use Mage_Core_Model_Security_HtmlEscapedString;
use Mage_Page_Block_Html_Header as Subject;
use OpenMage\Tests\Unit\OpenMageTest;

final class HeaderTest extends OpenMageTest
{
    private static Subject $subject;

    protected function setUp(): void
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
        self::assertInstanceOf(Subject::class, self::$subject->setLogo('src', 'alt'));
    }

    /**
     * @group Block
     */
    public function testGetLogoSrc(): void
    {
        self::assertIsString(self::$subject->getLogoSrc());
    }

    /**
     * @group Block
     */
    public function testGetLogoSrcSmall(): void
    {
        self::assertIsString(self::$subject->getLogoSrcSmall());
    }

    /**
     * @group Block
     */
    public function testGetLogoAlt(): void
    {
        self::assertInstanceOf(Mage_Core_Model_Security_HtmlEscapedString::class, self::$subject->getLogoAlt());
    }
}
