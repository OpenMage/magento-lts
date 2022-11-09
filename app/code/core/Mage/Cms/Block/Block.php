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
 * @package    Mage_Cms
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Cms block content block
 *
 * @method int getBlockId()
 * @method $this setBlockId(int $int)
 *
 * @category   Mage
 * @package    Mage_Cms
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Cms_Block_Block extends Mage_Core_Block_Abstract
{
    /**
     * Initialize cache
     */
    protected function _construct()
    {
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
     * @throws Mage_Core_Model_Store_Exception
     * @throws Exception
     */
    protected function _toHtml()
    {
        $blockId = $this->getBlockId();
        $html = '';
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
