<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Oauth\Model;

use Mage_Core_Exception;
use Mage_Oauth_Model_Server as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use PHPUnit\Framework\MockObject\MockObject;
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
        $property->setAccessible(true);

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
}
