<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Tag
 */

/**
 * Tags list in customer's account
 *
 * @package    Mage_Tag
 */
class Mage_Tag_Block_Customer_Tags extends Mage_Customer_Block_Account_Dashboard
{
    protected $_tags;

    protected $_minPopularity;

    protected $_maxPopularity;

    protected function _loadTags()
    {
        if (empty($this->_tags)) {
            $this->_tags = [];

            $tags = Mage::getResourceModel('tag/tag_collection')
                ->addPopularity()
                ->setOrder('popularity', 'DESC')
                ->addCustomerFilter(Mage::getSingleton('customer/session')->getCustomerId())
                ->setActiveFilter()
                ->load()
                ->getItems();
        } else {
            return;
        }

        if (isset($tags) && count($tags) == 0) {
            return;
        }

        $this->_maxPopularity = reset($tags)->getPopularity();
        $this->_minPopularity = end($tags)->getPopularity();
        $range = $this->_maxPopularity - $this->_minPopularity;
        $range = ($range == 0) ? 1 : $range;

        /** @var Mage_Tag_Model_Tag $tag */
        foreach ($tags as $tag) {
            $tag->setRatio(($tag->getPopularity() - $this->_minPopularity) / $range);
            $this->_tags[$tag->getName()] = $tag;
        }

        ksort($this->_tags);
    }

    /**
     * @return Mage_Tag_Model_Tag[]
     */
    public function getTags()
    {
        $this->_loadTags();
        return $this->_tags;
    }

    /**
     * @return int
     */
    public function getMaxPopularity()
    {
        return $this->_maxPopularity;
    }

    /**
     * @return int
     */
    public function getMinPopularity()
    {
        return $this->_minPopularity;
    }
}
