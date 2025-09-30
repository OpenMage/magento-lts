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
 */
class Mage_Persistent_Block_Form_Remember extends Mage_Core_Block_Template
{
    /**
     * Prevent rendering if Persistent disabled
     *
     * @return string
     */
    protected function _toHtml()
    {
        /** @var Mage_Persistent_Helper_Data $helper */
        $helper = Mage::helper('persistent');
        return ($helper->isEnabled() && $helper->isRememberMeEnabled()) ? parent::_toHtml() : '';
    }

    /**
     * Is "Remember Me" checked
     *
     * @return bool
     */
    public function isRememberMeChecked()
    {
        /** @var Mage_Persistent_Helper_Data $helper */
        $helper = Mage::helper('persistent');
        return $helper->isEnabled() && $helper->isRememberMeEnabled() && $helper->isRememberMeCheckedDefault();
    }
}
