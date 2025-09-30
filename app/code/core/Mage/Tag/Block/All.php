<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Tag
 */

/**
 * All tags block
 *
 * @package    Mage_Tag
 */
class Mage_Tag_Block_All extends Mage_Core_Block_Template
{
    protected $_tags;
    protected $_minPopularity;
    protected $_maxPopularity;

    /**
     * @return $this
     * @throws Mage_Core_Model_Store_Exception
     */
    protected function _loadTags()
    {
        if (empty($this->_tags)) {
            $this->_tags = [];
            $tags = Mage::getModel('tag/tag')->getPopularCollection()
                ->joinFields(Mage::app()->getStore()->getId())
                ->limit(100)
                ->load()
                ->getItems();

            if (count($tags) == 0) {
                return $this;
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
        return $this;
    }

    /**
     * @return Mage_Tag_Model_Tag[]
     * @throws Mage_Core_Model_Store_Exception
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

    /**
     * @return string
     */
    protected function _getHeadText()
    {
        return Mage::helper('tag')->__('All Tags');
    }
}
