<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Core\Helper;

use Mage;
use Mage_Core_Helper_Cookie as Subject;
use OpenMage\Tests\Unit\OpenMageTest;

final class CookieTest extends OpenMageTest
{
    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::helper('core/cookie');
    }

    /**
     * @group Helper
     */
    public function testIsUserNotAllowSaveCookie(): void
    {
        self::assertIsBool(self::$subject->isUserNotAllowSaveCookie());
    }

    /**
     * @group Helper
     */
    public function testGetAcceptedSaveCookiesWebsiteIds(): void
    {
        self::assertSame('{"1":1}', self::$subject->getAcceptedSaveCookiesWebsiteIds());
    }

    /**
     * @covers Mage_Core_Helper_Cookie::getCookieRestrictionLifetime()
     * @group Helper
     */
    public function testGetCookieRestrictionLifetime(): void
    {
        self::assertSame(31536000, self::$subject->getCookieRestrictionLifetime());
    }

    /**
     * @covers Mage_Core_Helper_Cookie::getCookieRestrictionNoticeCmsBlockIdentifier()
     * @group Helper
     */
    public function testGetCookieRestrictionNoticeCmsBlockIdentifier(): void
    {
        self::assertSame('cookie_restriction_notice_block', self::$subject->getCookieRestrictionNoticeCmsBlockIdentifier());
    }
}
