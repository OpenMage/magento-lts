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

use Generator;
use Mage;
use Mage_Core_Helper_Url;
use PHPUnit\Framework\TestCase;

class UrlTest extends TestCase
{
    public const TEST_URL_BASE      = 'https://example.com';

    public const TEST_URL_PARAM     = 'https://example.com?foo=bar';

    public const TEST_URL_PARAMS    = 'https://example.com?foo=bar&BOO=baz';

    public const TEST_URL_SID1      = 'https://example.com?SID=S&foo=bar&BOO=baz';

    public const TEST_URL_SID2      = 'https://example.com?___SID=S&foo=bar&BOO=baz';

    public const TEST_URL_SID_BOTH  = 'https://example.com?___SID=S&SID=S&foo=bar&BOO=baz';

    public const TEST_URL_PUNY      = 'https://XN--example.com?foo=bar&BOO=baz';

    public Mage_Core_Helper_Url $subject;

    public function setUp(): void
    {
        Mage::app();
        $this->subject = Mage::helper('core/url');
    }

    /**
     * @covers Mage_Core_Helper_Url::getCurrentBase64Url()
     * @group Mage_Core
     * @group Mage_Core_Helper
     */
    public function testGetCurrentBase64Url(): void
    {
        $this->assertIsString($this->subject->getCurrentBase64Url());
    }

    /**
     * @covers Mage_Core_Helper_Url::getEncodedUrl()
     * @dataProvider provideGetEncodedUrl
     * @group Mage_Core
     * @group Mage_Core_Helper
     */
    public function testGetEncodedUrl(string $expectedResult, ?string $url): void
    {
        $this->assertSame($expectedResult, $this->subject->getEncodedUrl($url));
    }

    public function provideGetEncodedUrl(): Generator
    {
        yield 'null' => [
            'aHR0cDovLw,,',
            null,
        ];
        yield 'base url' => [
            'aHR0cHM6Ly9leGFtcGxlLmNvbQ,,',
            self::TEST_URL_BASE,
        ];
    }

    /**
     * @covers Mage_Core_Helper_Url::getHomeUrl()
     * @group Mage_Core
     * @group Mage_Core_Helper
     */
    public function testGetHomeUrl(): void
    {
        $this->assertIsString($this->subject->getHomeUrl());
    }

    /**
     * @covers Mage_Core_Helper_Url::addRequestParam()
     * @dataProvider provideAddRequestParam
     * @group Mage_Core
     * @group Mage_Core_Helper
     */
    public function testAddRequestParam(string $expectedResult, string $url, array $param): void
    {
        $this->assertSame($expectedResult, $this->subject->addRequestParam($url, $param));
    }

    public function provideAddRequestParam(): Generator
    {
        yield 'int key' => [
            self::TEST_URL_BASE . '?',
            self::TEST_URL_BASE,
            [0 => 'int'],
        ];
        yield 'int value' => [
            self::TEST_URL_BASE . '?int=0',
            self::TEST_URL_BASE,
            ['int' => 0],
        ];
        yield 'null' => [
            self::TEST_URL_BASE . '?null',
            self::TEST_URL_BASE,
            ['null' => null],
        ];
        yield 'string' => [
            self::TEST_URL_PARAM,
            self::TEST_URL_BASE,
            ['foo' => 'bar'],
        ];
        yield 'string extend' => [
            self::TEST_URL_PARAMS,
            self::TEST_URL_PARAM,
            ['BOO' => 'baz'],
        ];
        yield 'array' => [
            self::TEST_URL_BASE . '?key[]=subValue',
            self::TEST_URL_BASE,
            ['key' => ['subKey' => 'subValue']],
        ];
    }

    /**
     * @covers Mage_Core_Helper_Url::removeRequestParam()
     * @dataProvider provideRemoveRequestParam
     * @group Mage_Core
     * @group Mage_Core_Helper
     */
    public function testRemoveRequestParam(string $expectedResult, string $url, string $paramKey, bool $caseSensitive = false): void
    {
        $this->assertSame($expectedResult, $this->subject->removeRequestParam($url, $paramKey, $caseSensitive));
    }

    public function provideRemoveRequestParam(): Generator
    {
        yield 'remove #1' => [
            self::TEST_URL_BASE,
            self::TEST_URL_PARAM,
            'foo'
        ];
        yield 'remove #2' => [
            self::TEST_URL_PARAMS,
            self::TEST_URL_PARAMS,
            'boo'
        ];
        yield 'remove #1 case sensitive' => [
            self::TEST_URL_PARAM,
            self::TEST_URL_PARAM,
            'FOO',
            true
        ];
        yield 'remove #2 case sensitive' => [
            self::TEST_URL_PARAM,
            self::TEST_URL_PARAMS,
            'BOO',
            true
        ];
        yield 'not-exists' => [
            self::TEST_URL_PARAMS,
            self::TEST_URL_PARAMS,
            'not-exists'
        ];
        yield '___SID' => [
            self::TEST_URL_SID1,
            self::TEST_URL_SID_BOTH,
            '___SID'
        ];
        yield 'SID' => [
            self::TEST_URL_SID2,
            self::TEST_URL_SID_BOTH,
            'SID'
        ];
    }

    /**
     * @covers Mage_Core_Helper_Url::encodePunycode()
     * @group Mage_Core
     * @group Mage_Core_Helper
     */
    public function testEncodePunycode(): void
    {
        $this->assertSame(self::TEST_URL_BASE, $this->subject->encodePunycode(self::TEST_URL_BASE));
        $this->assertSame(self::TEST_URL_PUNY, $this->subject->encodePunycode(self::TEST_URL_PUNY));
        $this->markTestIncomplete('This test has to be checked.');
    }

    /**
     * @covers Mage_Core_Helper_Url::decodePunycode()
     * @group Mage_Core
     * @group Mage_Core_Helper
     */
    public function testDecodePunycode(): void
    {
        $this->assertSame(self::TEST_URL_BASE, $this->subject->decodePunycode(self::TEST_URL_BASE));
        $this->assertSame('https://?foo=bar&BOO=baz', $this->subject->decodePunycode(self::TEST_URL_PUNY));
        $this->markTestIncomplete('This test has to be checked.');
    }
}
