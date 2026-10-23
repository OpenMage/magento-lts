<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Api\Helper;

use Mage;
use Mage_Api_Helper_Data as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Api\Helper\DataTrait;
use Override;

/**
 * GHSA-23v7-qrrp-4m96
 */
final class DataTest extends OpenMageTest
{
    use DataTrait;

    private static Subject $subject;

    private ?string $originalHttpHost;

    private bool $originalDeveloperMode;

    #[Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::helper('api/data');
    }

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->originalHttpHost = $_SERVER['HTTP_HOST'] ?? null;
        $this->originalDeveloperMode = Mage::getIsDeveloperMode();
        Mage::setIsDeveloperMode(false);
    }

    #[Override]
    protected function tearDown(): void
    {
        if ($this->originalHttpHost === null) {
            unset($_SERVER['HTTP_HOST']);
        } else {
            $_SERVER['HTTP_HOST'] = $this->originalHttpHost;
        }

        Mage::setIsDeveloperMode($this->originalDeveloperMode);
        parent::tearDown();
    }

    public function testConfiguredHostsIncludeBaseUrlHost(): void
    {
        $baseHost = $this->getBaseHost();

        self::assertContains($baseHost, self::$subject->getConfiguredHosts());
        self::assertTrue(self::$subject->isConfiguredHost(strtoupper($baseHost)));
        self::assertFalse(self::$subject->isConfiguredHost('attacker.example'));
        self::assertFalse(self::$subject->isConfiguredHost(''));
    }

    public function testServiceUrlIgnoresForeignHostHeader(): void
    {
        $_SERVER['HTTP_HOST'] = 'attacker.example';

        $url = self::$subject->getServiceUrl('api/soap/index', ['_query' => ['wsdl' => 1]]);

        self::assertSame($this->getBaseHost(), parse_url($url, PHP_URL_HOST));
    }

    public function testServiceUrlKeepsConfiguredHostHeader(): void
    {
        $_SERVER['HTTP_HOST'] = strtoupper($this->getBaseHost()) . ':8080';

        $url = self::$subject->getServiceUrl('api/soap/index');

        self::assertSame(strtolower((string) parse_url($url, PHP_URL_HOST)), $this->getBaseHost());
    }

    public function testServiceUrlHonoursAnyHostHeaderInDeveloperMode(): void
    {
        Mage::setIsDeveloperMode(true);
        $_SERVER['HTTP_HOST'] = 'devbox'; // dotless: Zend_Validate_Hostname needs ext-intl for dotted names

        $url = self::$subject->getServiceUrl('api/soap/index');

        self::assertSame('devbox', parse_url($url, PHP_URL_HOST));
    }

    private function getBaseHost(): string
    {
        return strtolower((string) parse_url((string) Mage::app()->getStore(0)->getConfig('web/unsecure/base_url'), PHP_URL_HOST));
    }
}
