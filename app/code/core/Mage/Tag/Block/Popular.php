<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Tag
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Popular tags block
 *
 * @category   Mage
 * @package    Mage_Tag
 */
class Mage_Tag_Block_Popular extends Mage_Core_Block_Template
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
                ->limit(20)
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
     * @throws Mage_Core_Model_Store_Exception
     */
    protected function _toHtml()
    {
        if (count($this->getTags()) > 0) {
            return parent::_toHtml();
        }
        return '';
    }
}
