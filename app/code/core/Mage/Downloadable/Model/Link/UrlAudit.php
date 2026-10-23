<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Downloadable
 */

/**
 * Audit of stored URL type links and samples against the download destination checks
 *
 * Downloads from private, loopback and other restricted addresses are refused at fetch
 * time (GHSA-68jq-pfh7-9pjx). A store that legitimately served files from such a host
 * would otherwise only learn about it from customers whose downloads fail. This audit runs
 * from cron, once per check version and "allow private network" setting, and tells the
 * merchant through the admin inbox which existing link hosts are refused, and which are
 * only accepted because private networks are allowed.
 *
 * @package    Mage_Downloadable
 */
class Mage_Downloadable_Model_Link_UrlAudit
{
    public const FLAG_CODE = 'downloadable_link_url_audit';

    /**
     * Bump to make every store run the audit again after the destination checks change
     */
    public const AUDIT_VERSION = '1';

    /**
     * Upper bound on distinct hosts resolved in one run, to bound DNS time in cron
     */
    public const MAX_ORIGINS = 200;

    /**
     * Upper bound on hosts named in one inbox message
     */
    public const MAX_LISTED_ORIGINS = 20;

    /**
     * Cron entry point
     */
    public function run(): void
    {
        $state = self::AUDIT_VERSION . ':' . (int) $this->_getDownloadHelper()->isPrivateNetworkAllowed();

        /** @var Mage_Core_Model_Flag $flag */
        $flag = Mage::getModel('core/flag', ['flag_code' => self::FLAG_CODE])->loadSelf();
        if ($flag->getFlagData() === $state) {
            return;
        }

        $this->_notify($this->audit($this->_getStoredUrls()));

        $flag->setFlagData($state)->save();
    }

    /**
     * Inspect one URL per origin
     *
     * Trusted prefixes are matched per URL, not per host, so the URL inspected for an
     * origin is the first one that no trusted prefix covers. Origins whose URLs are all
     * covered are skipped without a DNS lookup.
     *
     * @param  string[]                                                         $urls
     * @return array{refused: array<string, string>, private: array<string, string>}
     */
    public function audit(array $urls): array
    {
        $helper = $this->_getDownloadHelper();

        $byOrigin = [];
        foreach (array_unique(array_filter(array_map('strval', $urls))) as $url) {
            $parts = parse_url($url);
            $origin = is_array($parts) && isset($parts['scheme'], $parts['host'])
                ? strtolower($parts['scheme']) . '://' . strtolower($parts['host']) . (isset($parts['port']) ? ':' . $parts['port'] : '')
                : $url;
            if (!isset($byOrigin[$origin]) && !$helper->isTrustedLinkUrl($url)) {
                $byOrigin[$origin] = $url;
            }
        }

        $refused = [];
        $private = [];
        foreach (array_slice($byOrigin, 0, self::MAX_ORIGINS, true) as $url) {
            $result = $helper->inspectLinkUrl($url);
            if (!$result['allowed']) {
                $refused[$result['origin']] = (string) ($result['address'] ?? $result['reason']);
            } elseif ($result['address_class'] === Mage_Downloadable_Helper_Download::ADDRESS_PRIVATE) {
                $private[$result['origin']] = (string) $result['address'];
            }
        }

        return ['refused' => $refused, 'private' => $private];
    }

    /**
     * @return string[]
     */
    protected function _getStoredUrls(): array
    {
        /** @var Mage_Core_Model_Resource $resource */
        $resource = Mage::getSingleton('core/resource');
        $adapter = $resource->getConnection('core_read');
        $type = Mage_Downloadable_Helper_Download::LINK_TYPE_URL;

        $selects = [
            $adapter->select()
                ->from($resource->getTableName('downloadable/link'), ['url' => 'link_url'])
                ->where('link_type = ?', $type),
            $adapter->select()
                ->from($resource->getTableName('downloadable/link'), ['url' => 'sample_url'])
                ->where('sample_type = ?', $type),
            $adapter->select()
                ->from($resource->getTableName('downloadable/sample'), ['url' => 'sample_url'])
                ->where('sample_type = ?', $type),
            $adapter->select()
                ->from($resource->getTableName('downloadable/link_purchased_item'), ['url' => 'link_url'])
                ->where('link_type = ?', $type),
        ];

        $urls = [];
        foreach ($selects as $select) {
            $select->distinct();
            foreach ($adapter->fetchCol($select) as $url) {
                $urls[] = (string) $url;
            }
        }

        return $urls;
    }

    /**
     * @param array{refused: array<string, string>, private: array<string, string>} $report
     */
    protected function _notify(array $report): void
    {
        $helper = Mage::helper('downloadable');
        $location = 'System > Configuration > Catalog > Downloadable Product Options';

        if ($report['refused']) {
            $this->_addInboxMessage(
                $helper->__('%d downloadable product link host(s) will be refused', count($report['refused'])),
                $helper->__(
                    'URL type links and samples on these hosts resolve to a private, loopback or otherwise restricted address and can no longer be downloaded by customers: %s. If a host is yours and trusted, add its URL prefix to Trusted Link URL Prefixes in %s.',
                    $this->_formatOrigins($report['refused']),
                    $location,
                ),
            );
        }

        if ($report['private']) {
            $this->_addInboxMessage(
                $helper->__('%d downloadable product link host(s) are on a private network', count($report['private'])),
                $helper->__(
                    'URL type links and samples on these hosts resolve to a private network address and are only downloadable because Allow Link URLs on Private Networks is enabled: %s. Add trusted hosts to Trusted Link URL Prefixes in %s so that the setting can be turned off; OpenMage 20.19 and later turn it off by default.',
                    $this->_formatOrigins($report['private']),
                    $location,
                ),
            );
        }
    }

    protected function _addInboxMessage(string $title, string $description): void
    {
        /** @var Mage_AdminNotification_Model_Inbox $inbox */
        $inbox = Mage::getModel('adminnotification/inbox');
        $inbox->addMajor($title, $description);
    }

    /**
     * @param array<string, string> $origins origin => address or reason
     */
    protected function _formatOrigins(array $origins): string
    {
        $listed = [];
        foreach (array_slice($origins, 0, self::MAX_LISTED_ORIGINS, true) as $origin => $detail) {
            $listed[] = $origin . ' (' . $detail . ')';
        }

        $more = count($origins) - count($listed);
        if ($more > 0) {
            $listed[] = Mage::helper('downloadable')->__('and %d more', $more);
        }

        return implode(', ', $listed);
    }

    protected function _getDownloadHelper(): Mage_Downloadable_Helper_Download
    {
        return Mage::helper('downloadable/download');
    }
}
