<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Cms
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Cms Static Block Widget
 *
 * @category   Mage
 * @package    Mage_Cms
 * @author     Magento Core Team <core@magentocommerce.com>
 *
 * @method int getBlockId()
 * @method $this setText(string $value)
 */
class Mage_Cms_Block_Widget_Block extends Mage_Core_Block_Template implements Mage_Widget_Block_Interface
{
    /**
     * Initialize cache
     */
    protected function _construct()
    {
        parent::_construct();
        /*
        * setting cache to save the cms block
        */
        $this->setCacheTags([Mage_Cms_Model_Block::CACHE_TAG]);
        $this->setCacheLifetime(false);
    }

    /**
     * Storage for used widgets
     *
     * @var array
     */
    static protected $_widgetUsageMap = [];

    /**
     * Prepare block text and determine whether block output enabled or not
     * Prevent blocks recursion if needed
     *
     * @return $this
     */
    protected function _beforeToHtml()
    {
        parent::_beforeToHtml();
        $blockId = $this->getData('block_id');
        $blockHash = get_class($this) . $blockId;

        if (isset(self::$_widgetUsageMap[$blockHash])) {
            return $this;
        }
        self::$_widgetUsageMap[$blockHash] = true;

        if ($blockId) {
            $block = Mage::getModel('cms/block')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->load($blockId);
            if ($block->getIsActive()) {
                $helper = Mage::helper('cms');
                $processor = $helper->getBlockTemplateProcessor();
                if ($this->isRequestFromAdminArea()) {
                    $this->setText($processor->filter(
                        Mage::getSingleton('core/input_filter_maliciousCode')->filter($block->getContent())
                    ));
                } else {
                    $this->setText($processor->filter($block->getContent()));
                }
                $this->addModelTags($block);
            }
        }

        unset(self::$_widgetUsageMap[$blockHash]);
        return $this;
    }

    /**
     * Retrieve values of properties that unambiguously identify unique content
     *
     * @return array
     */
    public function getCacheKeyInfo()
    {
        $result = parent::getCacheKeyInfo();
        $blockId = $this->getBlockId();
        if ($blockId) {
            $result[] = $blockId;
        }
        return $result;
    }

    /**
     * Check is request goes from admin area
     *
     * @return bool
     */
    public function isRequestFromAdminArea()
    {
        return $this->getRequest()->getRouteName() === Mage_Core_Model_App_Area::AREA_ADMINHTML;
    }
}
