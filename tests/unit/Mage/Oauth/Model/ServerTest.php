<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Oauth\Model;

use Generator;
use Mage;
use Mage_Oauth_Exception;
use Mage_Oauth_Model_Server as Subject;
use Override;
use ReflectionMethod;
use ReflectionProperty;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Oauth\Model\ServerTrait;

final class ServerTest extends OpenMageTest
{
    use ServerTrait;

    private static Subject $subject;

    #[Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::getModel('oauth/server');
    }

    /**
     * @dataProvider validateCallbackUrlParamDataProvider
     * @group Model
     */
    public function testValidateCallbackUrlParam(bool $expected, string $callback, ?string $registeredCallbackUrl): void
    {
        $consumer = Mage::getModel('oauth/consumer');
        $consumer->setCallbackUrl($registeredCallbackUrl);

        (new ReflectionProperty(Subject::class, '_consumer'))->setValue(self::$subject, $consumer);
        (new ReflectionProperty(Subject::class, '_protocolParams'))
            ->setValue(self::$subject, ['oauth_callback' => $callback]);

        if (!$expected) {
            self::expectException(Mage_Oauth_Exception::class);
            self::expectExceptionCode(Subject::ERR_PARAMETER_REJECTED);
        }

        (new ReflectionMethod(Subject::class, '_validateCallbackUrlParam'))->invoke(self::$subject);

        self::assertTrue($expected);
    }

    /**
     * @return Generator<string, list{bool, string, null|string}, void, void>
     */
    public static function validateCallbackUrlParamDataProvider(): Generator
    {
        yield 'out of band with registered callback' => [
            true,
            Subject::CALLBACK_ESTABLISHED,
            'https://legit-app.com',
        ];

        yield 'out of band without registered callback' => [
            true,
            Subject::CALLBACK_ESTABLISHED,
            null,
        ];

        yield 'registered callback matched exactly' => [
            true,
            'https://legit-app.com/cb',
            'https://legit-app.com/cb',
        ];

        yield 'registered callback matched below its path' => [
            true,
            'https://legit-app.com/cb/done',
            'https://legit-app.com/cb',
        ];

        yield 'host suffix bypass is rejected' => [
            false,
            'https://legit-app.com.evil.com/cb',
            'https://legit-app.com',
        ];

        yield 'userinfo bypass is rejected' => [
            false,
            'https://legit-app.com@evil.com/cb',
            'https://legit-app.com',
        ];

        yield 'unrelated but well formed URL is rejected' => [
            false,
            'https://evil.com/cb',
            'https://legit-app.com',
        ];

        yield 'well formed URL without registered callback' => [
            true,
            'https://any-app.com/cb',
            null,
        ];

        yield 'malformed URL without registered callback' => [
            false,
            'not-a-url',
            null,
        ];
    }
}
