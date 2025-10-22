<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Rss
 */

/**
 * Auth session model
 *
 * @package    Mage_Rss
 */
class Mage_Rss_Model_Rss
{
    protected $_feedArray = [];

    /**
     * @param array $data
     * @return $this
     */
    public function _addHeader($data = [])
    {
        $this->_feedArray = $data;
        return $this;
    }

    /**
     * @param array $entries
     * @return $this
     */
    public function _addEntries($entries)
    {
        $this->_feedArray['entries'] = $entries;
        return $this;
    }

    /**
     * @param array $entry
     * @return $this
     */
    public function _addEntry($entry)
    {
        $this->_feedArray['entries'][] = $entry;
        return $this;
    }

    /**
     * @return array
     */
    public function getFeedArray()
    {
        return $this->_feedArray;
    }

    /**
     * @return string
     */
    public function createRssXml()
    {
        try {
            $rssFeedFromArray = Zend_Feed::importArray($this->getFeedArray(), 'rss');
            return $rssFeedFromArray->saveXml();
        } catch (Exception $exception) {
            return Mage::helper('rss')->__('Error in processing xml. %s', $exception->getMessage());
        }
    }
}
