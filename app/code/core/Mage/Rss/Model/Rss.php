<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Rss
 */

/**
 * Auth session model
 *
 * @category   Mage
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
        } catch (Exception $e) {
            return Mage::helper('rss')->__('Error in processing xml. %s', $e->getMessage());
        }
    }
}
