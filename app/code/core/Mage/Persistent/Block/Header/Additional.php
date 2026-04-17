<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Persistent
 */

/**
 * Remember Me block
 *
 * @package    Mage_Persistent
 *
 * @method $this setAnchorText(string $value)
 * @method $this setHref(string $value)
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
