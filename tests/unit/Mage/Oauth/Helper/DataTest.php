<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Oauth\Helper;

use Generator;
use Mage;
use Mage_Oauth_Helper_Data as Subject;
use Override;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Oauth\Helper\DataTrait;

final class DataTest extends OpenMageTest
{
    use DataTrait;

    private static Subject $subject;

    #[Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::helper('oauth/data');
    }

    /**
     * @dataProvider isCallbackUrlWhitelistedDataProvider
     * @group Helper
     */
    public function testIsCallbackUrlWhitelisted(bool $expected, string $callbackUrl, string $registeredCallbackUrl): void
    {
        self::assertSame($expected, self::$subject->isCallbackUrlWhitelisted($callbackUrl, $registeredCallbackUrl));
    }

    /**
     * @return Generator<string, list{bool, string, string}, void, void>
     */
    public static function isCallbackUrlWhitelistedDataProvider(): Generator
    {
        yield 'exact match' => [
            true,
            'https://legit-app.com/cb',
            'https://legit-app.com/cb',
        ];

        yield 'path below the registered path' => [
            true,
            'https://legit-app.com/cb/done?state=1',
            'https://legit-app.com/cb',
        ];

        yield 'any path below a registered host' => [
            true,
            'https://legit-app.com/anything',
            'https://legit-app.com',
        ];

        yield 'host case is ignored' => [
            true,
            'https://LEGIT-App.CoM/cb',
            'https://legit-app.com/cb',
        ];

        yield 'scheme case is ignored' => [
            true,
            'HTTPS://legit-app.com/cb',
            'https://legit-app.com/cb',
        ];

        yield 'default port equals omitted port' => [
            true,
            'https://legit-app.com:443/cb',
            'https://legit-app.com/cb',
        ];

        yield 'custom scheme without authority on full match' => [
            true,
            'myapp:/callback',
            'myapp:/callback',
        ];

        yield 'custom scheme with authority' => [
            true,
            'myapp://callback/done',
            'myapp://callback',
        ];

        yield 'host suffix bypass' => [
            false,
            'https://legit-app.com.evil.com/cb',
            'https://legit-app.com',
        ];

        yield 'userinfo bypass' => [
            false,
            'https://legit-app.com@evil.com/cb',
            'https://legit-app.com',
        ];

        yield 'subdomain is not covered' => [
            false,
            'https://evil.legit-app.com/cb',
            'https://legit-app.com',
        ];

        yield 'scheme mismatch' => [
            false,
            'http://legit-app.com/cb',
            'https://legit-app.com/cb',
        ];

        yield 'port mismatch' => [
            false,
            'https://legit-app.com:8443/cb',
            'https://legit-app.com/cb',
        ];

        yield 'path prefix without segment boundary' => [
            false,
            'https://legit-app.com/cbevil',
            'https://legit-app.com/cb',
        ];

        yield 'unparseable callback' => [
            false,
            'http://:80',
            'https://legit-app.com/cb',
        ];

        yield 'callback without scheme and host' => [
            false,
            'not-a-url',
            'https://legit-app.com/cb',
        ];

        yield 'out of band is not whitelisted' => [
            false,
            'oob',
            'https://legit-app.com/cb',
        ];

        yield 'empty callback' => [
            false,
            '',
            'https://legit-app.com/cb',
        ];

        yield 'empty registered callback' => [
            false,
            'https://legit-app.com/cb',
            '',
        ];
    }
}
