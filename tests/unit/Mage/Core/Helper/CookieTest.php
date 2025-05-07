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

namespace OpenMage\Tests\Unit\Mage\Core\Helper;

use Mage;
use Mage_Core_Helper_Cookie as Subject;
use OpenMage\Tests\Unit\OpenMageTest;

class CookieTest extends OpenMageTest
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
        static::assertIsBool(self::$subject->isUserNotAllowSaveCookie());
    }

    /**
     * @group Helper
     */
    public function testGetAcceptedSaveCookiesWebsiteIds(): void
    {
        static::assertSame('{"1":1}', self::$subject->getAcceptedSaveCookiesWebsiteIds());
    }

    /**
     * @covers Mage_Core_Helper_Cookie::getCookieRestrictionLifetime()
     * @group Helper
     */
    public function testGetCookieRestrictionLifetime(): void
    {
        static::assertSame(31536000, self::$subject->getCookieRestrictionLifetime());
    }

    /**
     * @covers Mage_Core_Helper_Cookie::getCookieRestrictionNoticeCmsBlockIdentifier()
     * @group Helper
     */
    public function testGetCookieRestrictionNoticeCmsBlockIdentifier(): void
    {
        static::assertSame('cookie_restriction_notice_block', self::$subject->getCookieRestrictionNoticeCmsBlockIdentifier());
    }
}
