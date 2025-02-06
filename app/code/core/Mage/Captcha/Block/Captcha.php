<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Captcha
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
