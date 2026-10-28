<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Downloadable\Model\Link;

use Mage;
use Mage_Downloadable_Helper_Download;
use OpenMage\Tests\Unit\OpenMageTest;

/**
 * GHSA-68jq-pfh7-9pjx
 */
final class UrlAuditTest extends OpenMageTest
{
    private UrlAuditTestFixture $subject;

    private UrlAuditTestHelperFixture $helper;

    private string $originalAllowedPrefixes;

    private string $originalAllowPrivateNetwork;

    protected function setUp(): void
    {
        parent::setUp();
        $this->helper = new UrlAuditTestHelperFixture();
        $this->subject = new UrlAuditTestFixture($this->helper);
        $this->originalAllowedPrefixes = (string) Mage::getStoreConfig(Mage_Downloadable_Helper_Download::XML_PATH_LINK_URL_ALLOWED_PREFIXES);
        $this->originalAllowPrivateNetwork = (string) Mage::getStoreConfig(Mage_Downloadable_Helper_Download::XML_PATH_LINK_URL_ALLOW_PRIVATE_NETWORK);
        $this->setConfig(Mage_Downloadable_Helper_Download::XML_PATH_LINK_URL_ALLOWED_PREFIXES, '');
        $this->setConfig(Mage_Downloadable_Helper_Download::XML_PATH_LINK_URL_ALLOW_PRIVATE_NETWORK, '0');
    }

    protected function tearDown(): void
    {
        $this->setConfig(Mage_Downloadable_Helper_Download::XML_PATH_LINK_URL_ALLOWED_PREFIXES, $this->originalAllowedPrefixes);
        $this->setConfig(Mage_Downloadable_Helper_Download::XML_PATH_LINK_URL_ALLOW_PRIVATE_NETWORK, $this->originalAllowPrivateNetwork);
        parent::tearDown();
    }

    public function testAuditGroupsByOriginAndClassifies(): void
    {
        $this->helper->dns = [
            'cdn.example' => ['93.184.216.34'],
            'nas.lan' => ['192.168.1.20'],
            'metadata.example' => ['169.254.169.254'],
        ];

        $report = $this->subject->audit([
            'https://cdn.example/a.zip',
            'https://cdn.example/b.zip',
            'http://nas.lan/a.zip',
            'http://nas.lan/b.zip',
            'http://metadata.example/latest/meta-data/',
            '',
        ]);

        self::assertSame(
            ['http://nas.lan' => '192.168.1.20', 'http://metadata.example' => '169.254.169.254'],
            $report['refused'],
        );
        self::assertSame([], $report['private']);
        self::assertSame(['cdn.example', 'nas.lan', 'metadata.example'], $this->helper->lookups);
    }

    public function testAuditListsPrivateHostsSeparatelyWhenPrivateNetworkIsAllowed(): void
    {
        $this->setConfig(Mage_Downloadable_Helper_Download::XML_PATH_LINK_URL_ALLOW_PRIVATE_NETWORK, '1');
        $this->helper->dns = [
            'nas.lan' => ['192.168.1.20'],
            'metadata.example' => ['169.254.169.254'],
        ];

        $report = $this->subject->audit(['http://nas.lan/a.zip', 'http://metadata.example/']);

        self::assertSame(['http://metadata.example' => '169.254.169.254'], $report['refused']);
        self::assertSame(['http://nas.lan' => '192.168.1.20'], $report['private']);
    }

    public function testAuditSkipsTrustedUrlsWithoutLookup(): void
    {
        $this->setConfig(Mage_Downloadable_Helper_Download::XML_PATH_LINK_URL_ALLOWED_PREFIXES, 'http://nas.lan/downloads/');
        $this->helper->dns = ['nas.lan' => ['192.168.1.20']];

        $report = $this->subject->audit(['http://nas.lan/downloads/a.zip']);
        self::assertSame(['refused' => [], 'private' => []], $report);
        self::assertSame([], $this->helper->lookups);

        $report = $this->subject->audit(['http://nas.lan/downloads/a.zip', 'http://nas.lan/other/b.zip']);
        self::assertSame(['http://nas.lan' => '192.168.1.20'], $report['refused']);
    }

    public function testAuditReportsUnresolvableAndInvalidUrls(): void
    {
        $report = $this->subject->audit(['http://gone.example/a.zip', 'ftp://files.example/a.zip']);

        self::assertSame('Cannot resolve the download URL host.', $report['refused']['http://gone.example']);
        self::assertSame('Invalid download URL scheme.', $report['refused']['ftp://files.example/a.zip']);
    }

    public function testNotifyAddsOneMessagePerCategory(): void
    {
        $this->subject->notify(['refused' => [], 'private' => []]);
        self::assertSame([], $this->subject->messages);

        $this->subject->notify([
            'refused' => ['http://metadata.example' => '169.254.169.254'],
            'private' => ['http://nas.lan' => '192.168.1.20'],
        ]);

        self::assertCount(2, $this->subject->messages);
        self::assertStringContainsString('http://metadata.example (169.254.169.254)', $this->subject->messages[0][1]);
        self::assertStringContainsString('http://nas.lan (192.168.1.20)', $this->subject->messages[1][1]);
    }

    private function setConfig(string $path, string $value): void
    {
        Mage::app()->getStore()->setConfig($path, $value);
    }
}
