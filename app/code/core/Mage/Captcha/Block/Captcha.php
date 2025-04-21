<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Captcha
 */

/**
 * Captcha block
 *
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
