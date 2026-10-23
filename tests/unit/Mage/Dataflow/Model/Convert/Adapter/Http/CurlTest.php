<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Dataflow\Model\Convert\Adapter\Http;

use Varien_Http_Adapter_Curl;
use Iterator;
use Mage;
use Mage_Dataflow_Model_Convert_Adapter_Http_Curl as Subject;
use Mage_Dataflow_Model_Convert_Exception;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Dataflow\Model\Convert\Adapter\Http\CurlTrait;

final class CurlTest extends OpenMageTest
{
    use CurlTrait;

    private CurlTestFixture $subject;

    private string $originalAllowedPrefixes;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = new CurlTestFixture();
        $this->originalAllowedPrefixes = (string) Mage::getStoreConfig(Subject::XML_PATH_ALLOWED_URL_PREFIXES);
        $this->setAllowedPrefixes('');
    }

    protected function tearDown(): void
    {
        $this->setAllowedPrefixes($this->originalAllowedPrefixes);
        parent::tearDown();
    }

    /**
     * @dataProvider provideInvalidProtocols
     */
    public function testValidateUriRejectsNonHttpProtocols(string $uri): void
    {
        $this->expectException(Mage_Dataflow_Model_Convert_Exception::class);
        $this->expectExceptionMessage("Expecting a valid 'uri' parameter with http or https protocol");

        $this->subject->setVar('uri', $uri);
        $this->invokeValidateUri();
    }

    public function testValidateUriAllowsHttpAndHttpsWhenWhitelistIsEmpty(): void
    {
        $this->subject->setVar('uri', 'https://example.com/import.csv');

        $this->invokeValidateUri();

        self::addToAssertionCount(1);
    }

    public function testValidateUriRejectsUrlOutsideConfiguredWhitelist(): void
    {
        $this->setAllowedPrefixes("https://allowed.example/imports/\nhttps://cdn.example/");

        $this->expectException(Mage_Dataflow_Model_Convert_Exception::class);
        $this->expectExceptionMessage("The 'uri' parameter is not allowed by the configured URL prefix whitelist");

        $this->subject->setVar('uri', 'https://blocked.example/imports/products.csv');
        $this->invokeValidateUri();
    }

    public function testValidateUriAllowsUrlInsideConfiguredWhitelist(): void
    {
        $this->setAllowedPrefixes("https://allowed.example/imports/\nhttps://cdn.example/");
        $this->subject->setVar('uri', 'https://allowed.example/imports/products.csv');

        $this->invokeValidateUri();

        self::addToAssertionCount(1);
    }

    /**
     * @dataProvider provideUrlsOutsideConfiguredPrefix
     */
    public function testValidateUriRejectsPrefixLookalikes(string $uri): void
    {
        $this->setAllowedPrefixes('https://allowed.example');

        $this->expectException(Mage_Dataflow_Model_Convert_Exception::class);
        $this->expectExceptionMessage("The 'uri' parameter is not allowed by the configured URL prefix whitelist");

        $this->subject->setVar('uri', $uri);
        $this->invokeValidateUri();
    }

    /**
     * @dataProvider provideUrlsInsideConfiguredPrefix
     */
    public function testValidateUriAllowsUrlsUnderHostOnlyPrefix(string $uri): void
    {
        $this->setAllowedPrefixes('https://allowed.example');
        $this->subject->setVar('uri', $uri);

        $this->invokeValidateUri();

        self::addToAssertionCount(1);
    }

    public function testValidateUriRejectsDotSegmentsEscapingPathPrefix(): void
    {
        $this->setAllowedPrefixes('https://allowed.example/imports/');

        $this->expectException(Mage_Dataflow_Model_Convert_Exception::class);

        $this->subject->setVar('uri', 'https://allowed.example/imports/../admin/secret.csv');
        $this->invokeValidateUri();
    }

    public static function provideUrlsOutsideConfiguredPrefix(): Iterator
    {
        yield 'longer host' => ['https://allowed.example.attacker.test/products.csv'];
        yield 'userinfo' => ['https://allowed.example@169.254.169.254/latest/meta-data/'];
        yield 'userinfo with password' => ['https://allowed.example:x@127.0.0.1/'];
        yield 'other port' => ['https://allowed.example:8443/products.csv'];
        yield 'other scheme' => ['http://allowed.example/products.csv'];
    }

    public static function provideUrlsInsideConfiguredPrefix(): Iterator
    {
        yield 'root' => ['https://allowed.example'];
        yield 'path' => ['https://allowed.example/imports/products.csv'];
        yield 'query' => ['https://allowed.example/export?format=csv'];
        yield 'explicit default port' => ['https://allowed.example:443/products.csv'];
        yield 'host case' => ['https://Allowed.Example/products.csv'];
    }

    public function testRestrictProtocolsAddsCurlProtocolOptions(): void
    {
        $http = new Varien_Http_Adapter_Curl();

        $this->subject->restrictProtocolsWrapper($http);

        $options = (function (): array {
            return $this->_options;
        })->call($http);
        $expectedProtocols = CURLPROTO_HTTP | CURLPROTO_HTTPS;

        if (defined('CURLOPT_PROTOCOLS')) {
            self::assertSame($expectedProtocols, $options[CURLOPT_PROTOCOLS]);
        }

        if (defined('CURLOPT_REDIR_PROTOCOLS')) {
            self::assertSame($expectedProtocols, $options[CURLOPT_REDIR_PROTOCOLS]);
        }
    }

    public static function provideInvalidProtocols(): Iterator
    {
        yield 'file' => ['file:///etc/passwd'];
        yield 'gopher' => ['gopher://127.0.0.1:11211/_stats'];
        yield 'ftp' => ['ftp://example.com/import.csv'];
    }

    private function invokeValidateUri(): void
    {
        $this->subject->validateUriWrapper($this->subject->getVar('uri'));
    }

    private function setAllowedPrefixes(string $value): void
    {
        Mage::app()->getStore()->setConfig(Subject::XML_PATH_ALLOWED_URL_PREFIXES, $value);
    }
}
