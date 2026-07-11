<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Page
 */

/**
 * Html notices block
 *
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
