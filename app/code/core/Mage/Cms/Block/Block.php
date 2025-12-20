<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Cms
 */

/**
 * Cms block content block
 *
 * @method int   getBlockId()
 * @method $this setBlockId(int $int)
 *
 * @package    Mage_Cms
 */
class Mage_Cms_Block_Block extends Mage_Core_Block_Abstract
{
    /**
     * @inheritDoc
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
     * Prepare Content HTML
     *
     * @return string
     * @throws Exception
     * @throws Mage_Core_Model_Store_Exception
     */
    protected function _toHtml()
    {
        $html = parent::_toHtml();

        $blockId = $this->getBlockId();
        if ($blockId) {
            $block = Mage::getModel('cms/block')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->load($blockId);
            if ($block->getIsActive()) {
                /** @var Mage_Cms_Helper_Data $helper */
                $helper = Mage::helper('cms');
                $processor = $helper->getBlockTemplateProcessor();
                $html = $processor->filter($block->getContent());
                $this->addModelTags($block);
            }
        }

        return $html;
    }

    /**
     * Retrieve values of properties that unambiguously identify unique content
     *
     * @return array
     * @throws Mage_Core_Exception
     * @throws Mage_Core_Model_Store_Exception
     */
    public function getCacheKeyInfo()
    {
        $blockId = $this->getBlockId();
        if ($blockId) {
            $result = [
                'CMS_BLOCK',
                $blockId,
                Mage::app()->getStore()->getCode(),
            ];
        } else {
            $result = parent::getCacheKeyInfo();
        }

        return $result;
    }
}
