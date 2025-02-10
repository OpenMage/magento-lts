<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @group Mage_Core
 * @group Mage_Core_Helper
 */
declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Core\Helper;

use Mage;
use Mage_Core_Helper_Cookie as Subject;
use PHPUnit\Framework\TestCase;

class CookieTest extends TestCase
{
    public Subject $subject;

    public function setUp(): void
    {
        Mage::app();
        $this->subject = Mage::helper('core/cookie');
    }

    
    public function testIsUserNotAllowSaveCookie(): void
    {
        $this->assertIsBool($this->subject->isUserNotAllowSaveCookie());
    }

    
    public function testGetAcceptedSaveCookiesWebsiteIds(): void
    {
        $this->assertSame('{"1":1}', $this->subject->getAcceptedSaveCookiesWebsiteIds());
    }

    /**
     * @covers Mage_Core_Helper_Cookie::getCookieRestrictionLifetime()
     * @group Mage_Core
     * @group Mage_Core_Helper
     */
    public function testGetCookieRestrictionLifetime(): void
    {
        $this->assertSame(31536000, $this->subject->getCookieRestrictionLifetime());
    }

    /**
     * @covers Mage_Core_Helper_Cookie::getCookieRestrictionNoticeCmsBlockIdentifier()
     * @group Mage_Core
     * @group Mage_Core_Helper
     */
    public function testGetCookieRestrictionNoticeCmsBlockIdentifier(): void
    {
        $this->assertSame('cookie_restriction_notice_block', $this->subject->getCookieRestrictionNoticeCmsBlockIdentifier());
    }
}
