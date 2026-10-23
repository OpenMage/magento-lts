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
use Mage_Core_Exception;
use Mage_Oauth_Exception;
use Mage_Oauth_Model_Server as Subject;
use Override;
use OpenMage\Tests\Unit\OpenMageTest;
use PHPUnit\Framework\MockObject\MockObject;
use ReflectionMethod;
use ReflectionProperty;
use Zend_Controller_Request_Http;

/**
 * Regression tests for the OAuth authorize CSRF fix (GHSA-grrq-9gq2-vm55):
 * granting a third-party consumer access must require an authenticated POST
 * with a valid form key, and the parameters used for the grant must come
 * exclusively from the POST body, never from the (attacker-forgeable) query string.
 */
final class ServerTest extends OpenMageTest
{
    private static Subject $subject;

    #[Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::getModel('oauth/server');
    }

    /**
     * @return MockObject&Zend_Controller_Request_Http
     */
    private function createRequestMock(): MockObject
    {
        return $this->createMock(Zend_Controller_Request_Http::class);
    }

    /**
     * @return array<string, string>
     */
    private function getProtocolParams(Subject $server): array
    {
        $property = new ReflectionProperty(Subject::class, '_protocolParams');

        return $property->getValue($server);
    }

    /**
     * @group Model
     */
    public function testCheckAuthorizeRequestRejectsNonPostRequestWhenPostRequired(): void
    {
        $request = $this->createRequestMock();
        $request->method('isPost')->willReturn(false);

        $server = new Subject($request);

        self::expectException(Mage_Core_Exception::class);
        self::expectExceptionMessage('Request is not POST');

        $server->checkAuthorizeRequest(true);
    }

    /**
     * @group Model
     */
    public function testCheckAuthorizeRequestRejectsNonGetRequestWhenGetRequired(): void
    {
        $request = $this->createRequestMock();
        $request->method('isGet')->willReturn(false);

        $server = new Subject($request);

        self::expectException(Mage_Core_Exception::class);
        self::expectExceptionMessage('Request is not GET');

        $server->checkAuthorizeRequest();
    }

    /**
     * @group Model
     */
    public function testCheckAuthorizeRequestReadsProtocolParamsFromPostBodyOnly(): void
    {
        $request = $this->createRequestMock();
        $request->method('isPost')->willReturn(true);
        $request->method('getPost')->willReturn([
            'oauth_token' => 'a-post-token',
            'plain_param' => 'ignored',
        ]);
        // A CSRF attacker only controls the query string of a forged link/auto-submit
        // form; the fix must not fall back to it once the request is treated as POST.
        $request->expects(self::never())->method('getQuery');

        $server = $this->getMockBuilder(Subject::class)
            ->setConstructorArgs([$request])
            ->onlyMethods(['_initToken'])
            ->getMock();
        $server->expects(self::once())->method('_initToken');

        $server->checkAuthorizeRequest(true);

        self::assertSame(
            ['oauth_token' => 'a-post-token'],
            $this->getProtocolParams($server),
        );
    }

    /**
     * @group Model
     */
    public function testCheckAuthorizeRequestReadsProtocolParamsFromQueryStringInGetMode(): void
    {
        $request = $this->createRequestMock();
        $request->method('isGet')->willReturn(true);
        $request->method('getQuery')->willReturn([
            'oauth_token' => 'a-get-token',
            'plain_param' => 'ignored',
        ]);

        $server = $this->getMockBuilder(Subject::class)
            ->setConstructorArgs([$request])
            ->onlyMethods(['_initToken'])
            ->getMock();
        $server->expects(self::once())->method('_initToken');

        $server->checkAuthorizeRequest();

        self::assertSame(
            ['oauth_token' => 'a-get-token'],
            $this->getProtocolParams($server),
        );
    }

    /**
     * @group Model
     */
    public function testAuthorizeTokenAlwaysRequiresPostRequest(): void
    {
        $request = $this->createRequestMock();
        $request->method('isPost')->willReturn(false);

        $server = new Subject($request);

        self::expectException(Mage_Core_Exception::class);
        self::expectExceptionMessage('Request is not POST');

        $server->authorizeToken(1, 'customer');
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
