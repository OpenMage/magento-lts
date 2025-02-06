<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Page
 */

/**
 * Html notices block
 *
 * @category   Mage
 * @package    Mage_Page
 */
class Mage_Page_Block_Html_CookieNotice extends Mage_Core_Block_Template
{
    /**
     * Get content for cookie restriction block
     *
     * @return string
     */
    public function getCookieRestrictionBlockContent()
    {
        $blockIdentifier = Mage::helper('core/cookie')->getCookieRestrictionNoticeCmsBlockIdentifier();
        $block = Mage::getModel('cms/block')->setStoreId(Mage::app()->getStore()->getId());
        $block->load($blockIdentifier, 'identifier');

        $html = '';
        if ($block->getIsActive()) {
            /** @var Mage_Cms_Helper_Data $helper */
            $helper = Mage::helper('cms');
            $processor = $helper->getBlockTemplateProcessor();
            $html = $processor->filter($block->getContent());
        }

        return $html;
    }
}
