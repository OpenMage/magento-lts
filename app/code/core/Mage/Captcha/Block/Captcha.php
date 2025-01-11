<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Captcha
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Captcha block
 *
 * @category   Core
 * @package    Mage_Captcha
 *
 * @method string getFormId()
 */
class Mage_Captcha_Block_Captcha extends Mage_Core_Block_Template
{
    /**
     * Renders captcha HTML (if required)
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (Mage::helper('captcha')->isEnabled()) {
            $blockPath = Mage::helper('captcha')->getCaptcha($this->getFormId())->getBlockName();
            $block = $this->getLayout()->createBlock($blockPath);
            $block->setData($this->getData());
            return $block->toHtml();
        }
        return '';
    }
}
