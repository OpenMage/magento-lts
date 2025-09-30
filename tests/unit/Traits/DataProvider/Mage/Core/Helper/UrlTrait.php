<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Traits\DataProvider\Mage\Core\Helper;

use Generator;

trait UrlTrait
{
    public static string $testUrlBase      = 'https://example.com';

    public static string $testUrlParam     = 'https://example.com?foo=bar';

    public static string $testUrlParams    = 'https://example.com?foo=bar&BOO=baz';

    public static string $testUrlSid1      = 'https://example.com?SID=S&foo=bar&BOO=baz';

    public static string $testUrlSid2      = 'https://example.com?___SID=S&foo=bar&BOO=baz';

    public static string $testUrlSidBoth  = 'https://example.com?___SID=S&SID=S&foo=bar&BOO=baz';

    public static string $testUrlPuny     = 'https://XN--example.com?foo=bar&BOO=baz';

    public function provideGetEncodedUrl(): Generator
    {
        yield 'null' => [
            'aHR0cDovLw,,',
            null,
        ];
        yield 'base url' => [
            'aHR0cHM6Ly9leGFtcGxlLmNvbQ,,',
            self::$testUrlBase,
        ];
    }

    public function provideAddRequestParam(): Generator
    {
        yield 'int key' => [
            self::$testUrlBase . '?',
            self::$testUrlBase,
            [0 => 'int'],
        ];
        yield 'int value' => [
            self::$testUrlBase . '?int=0',
            self::$testUrlBase,
            ['int' => 0],
        ];
        yield 'null' => [
            self::$testUrlBase . '?null',
            self::$testUrlBase,
            ['null' => null],
        ];
        yield 'string' => [
            self::$testUrlParam,
            self::$testUrlBase,
            ['foo' => 'bar'],
        ];
        yield 'string extend' => [
            self::$testUrlParams,
            self::$testUrlParam,
            ['BOO' => 'baz'],
        ];
        yield 'array' => [
            self::$testUrlBase . '?key[]=subValue',
            self::$testUrlBase,
            ['key' => ['subKey' => 'subValue']],
        ];
    }

    public function provideRemoveRequestParam(): Generator
    {
        yield 'remove #1' => [
            self::$testUrlBase,
            self::$testUrlParam,
            'foo',
        ];
        yield 'remove #2' => [
            self::$testUrlParams,
            self::$testUrlParams,
            'boo',
        ];
        yield 'remove #1 case sensitive' => [
            self::$testUrlParam,
            self::$testUrlParam,
            'FOO',
            true,
        ];
        yield 'remove #2 case sensitive' => [
            self::$testUrlParam,
            self::$testUrlParams,
            'BOO',
            true,
        ];
        yield 'not-exists' => [
            self::$testUrlParams,
            self::$testUrlParams,
            'not-exists',
        ];
        yield '___SID' => [
            self::$testUrlSid1,
            self::$testUrlSidBoth,
            '___SID',
        ];
        yield 'SID' => [
            self::$testUrlSid2,
            self::$testUrlSidBoth,
            'SID',
        ];
    }
}
