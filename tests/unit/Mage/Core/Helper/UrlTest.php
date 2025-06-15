<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Core\Helper;

use Exception;
use Mage;
use Mage_Core_Helper_Url as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Core\Helper\UrlTrait;

class UrlTest extends OpenMageTest
{
    use UrlTrait;

    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::helper('core/url');
    }

    /**
     * @covers Mage_Core_Helper_Url::getCurrentBase64Url()
     * @group Helper
     */
    public function testGetCurrentBase64Url(): void
    {
        static::assertIsString(self::$subject->getCurrentBase64Url());
    }

    /**
     * @covers Mage_Core_Helper_Url::getEncodedUrl()
     * @dataProvider provideGetEncodedUrl
     * @group Helper
     */
    public function testGetEncodedUrl(string $expectedResult, ?string $url): void
    {
        static::assertSame($expectedResult, self::$subject->getEncodedUrl($url));
    }

    /**
     * @covers Mage_Core_Helper_Url::getHomeUrl()
     * @group Helper
     */
    public function testGetHomeUrl(): void
    {
        static::assertIsString(self::$subject->getHomeUrl());
    }

    /**
     * @covers Mage_Core_Helper_Url::addRequestParam()
     * @dataProvider provideAddRequestParam
     * @group Helper
     */
    public function testAddRequestParam(string $expectedResult, string $url, array $param): void
    {
        static::assertSame($expectedResult, self::$subject->addRequestParam($url, $param));
    }

    /**
     * @covers Mage_Core_Helper_Url::removeRequestParam()
     * @dataProvider provideRemoveRequestParam
     * @group Helper
     */
    public function testRemoveRequestParam(string $expectedResult, string $url, string $paramKey, bool $caseSensitive = false): void
    {
        static::assertSame($expectedResult, self::$subject->removeRequestParam($url, $paramKey, $caseSensitive));
    }

    /**
     * @covers Mage_Core_Helper_Url::encodePunycode()
     * @group Helper
     */
    public function testEncodePunycode(): void
    {
        static::assertSame(self::$testUrlBase, self::$subject->encodePunycode(self::$testUrlBase));
        static::assertSame(self::$testUrlPuny, self::$subject->encodePunycode(self::$testUrlPuny));
    }

    /**
     * @covers Mage_Core_Helper_Url::decodePunycode()
     * @group Helper
     * @throws Exception
     */
    public function testDecodePunycode(): void
    {
        static::assertSame(self::$testUrlBase, self::$subject->decodePunycode(self::$testUrlBase));
        static::assertSame('https://?foo=bar&BOO=baz', self::$subject->decodePunycode(self::$testUrlPuny));
    }
}
