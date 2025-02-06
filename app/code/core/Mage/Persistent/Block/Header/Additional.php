<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Persistent
 */

/**
 * Remember Me block
 *
 * @category   Mage
 * @package    Mage_Persistent
 *
 * @method $this setHref(string $value)
 * @method $this setAnchorText(string $value)
 */
class Mage_Persistent_Block_Header_Additional extends Mage_Core_Block_Html_Link
{
    /**
     * Render additional header html
     *
     * @return string
     */
    protected function _toHtml()
    {
        $text = $this->__('(Not %s?)', $this->escapeHtml(Mage::helper('persistent/session')->getCustomer()->getName()));

        $this->setAnchorText($text);
        $this->setHref($this->getUrl('persistent/index/unsetCookie'));

        return parent::_toHtml();
    }
}
