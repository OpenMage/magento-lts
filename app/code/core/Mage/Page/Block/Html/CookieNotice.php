<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Page
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
