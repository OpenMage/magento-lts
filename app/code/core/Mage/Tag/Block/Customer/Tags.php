<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Tag
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Tags list in customer's account
 *
 * @category   Mage
 * @package    Mage_Tag
 * @author     Magento Core Team <core@magentocommerce.com>
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
                ->addPopularity(null, Mage::app()->getStore()->getId())
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
