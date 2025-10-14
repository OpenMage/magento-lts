<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Cms
 */

/**
 * Cms Static Block Widget
 *
 * @package    Mage_Cms
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
    protected static $_widgetUsageMap = [];

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
        $blockHash = static::class . $blockId;

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
                        Mage::getSingleton('core/input_filter_maliciousCode')->filter($block->getContent()),
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
