<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_AdminNotification
 */

/**
 * AdminNotification Feed model
 *
 * @package    Mage_AdminNotification
 */
class Mage_AdminNotification_Model_Feed extends Mage_Core_Model_Abstract
{
    public const XML_USE_HTTPS_PATH    = 'system/adminnotification/use_https';
    public const XML_FEED_URL_PATH     = 'system/adminnotification/feed_url';
    public const XML_FREQUENCY_PATH    = 'system/adminnotification/frequency';
    public const XML_LAST_UPDATE_PATH  = 'system/adminnotification/last_update';

    /**
     * Feed url
     *
     * @var string|null
     */
    protected $_feedUrl;

    /**
     * Init model
     *
     */
    protected function _construct() {}

    /**
     * Retrieve feed url
     *
     * @return string
     */
    public function getFeedUrl()
    {
        if (is_null($this->_feedUrl)) {
            $this->_feedUrl = 'https://' . Mage::getStoreConfig(self::XML_FEED_URL_PATH);
        }
        return $this->_feedUrl;
    }

    /**
     * Check feed for modification
     *
     * @return $this
     */
    public function checkUpdate()
    {
        if (($this->getFrequency() + $this->getLastUpdate()) > time()) {
            return $this;
        }

        $feedData = [];

        $feedXml = $this->getFeedData();

        if ($feedXml && $feedXml->channel && $feedXml->channel->item) {
            foreach ($feedXml->channel->item as $item) {
                $feedData[] = [
                    'severity'      => (int) $item->severity,
                    'date_added'    => $this->getDate((string) $item->pubDate),
                    'title'         => (string) $item->title,
                    'description'   => (string) $item->description,
                    'url'           => (string) $item->link,
                ];
            }

            if ($feedData) {
                Mage::getModel('adminnotification/inbox')->parse(array_reverse($feedData));
            }
        }
        $this->setLastUpdate();

        return $this;
    }

    /**
     * Retrieve DB date from RSS date
     *
     * @param string $rssDate
     * @return string YYYY-MM-DD YY:HH:SS
     */
    public function getDate($rssDate)
    {
        return gmdate(Varien_Db_Adapter_Pdo_Mysql::TIMESTAMP_FORMAT, strtotime($rssDate));
    }

    /**
     * Retrieve Update Frequency
     *
     * @return int
     */
    public function getFrequency()
    {
        return Mage::getStoreConfig(self::XML_FREQUENCY_PATH) * 3600;
    }

    /**
     * Retrieve Last update time
     *
     * @return string|false
     */
    public function getLastUpdate()
    {
        return Mage::app()->loadCache('admin_notifications_lastcheck');
    }

    /**
     * Set last update time (now)
     *
     * @return $this
     */
    public function setLastUpdate()
    {
        Mage::app()->saveCache(time(), 'admin_notifications_lastcheck');
        return $this;
    }

    /**
     * Retrieve feed data as XML element
     *
     * @return SimpleXMLElement|false
     */
    public function getFeedData()
    {
        $curl = new Varien_Http_Adapter_Curl();
        $curl->setConfig([
            'timeout'   => 2,
        ]);
        $curl->write(Zend_Http_Client::GET, $this->getFeedUrl(), '1.0');
        $data = $curl->read();
        if ($data === false) {
            return false;
        }
        $data = preg_split('/^\r?$/m', $data, 2);
        $data = trim($data[1]);
        $curl->close();

        try {
            $xml  = new SimpleXMLElement($data);
        } catch (Exception $e) {
            return false;
        }

        return $xml;
    }

    /**
     * @return SimpleXMLElement
     */
    public function getFeedXml()
    {
        try {
            $data = $this->getFeedData();
            $xml  = new SimpleXMLElement($data);
        } catch (Exception $e) {
            $xml  = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8" ?><feed />');
        }

        return $xml;
    }
}
