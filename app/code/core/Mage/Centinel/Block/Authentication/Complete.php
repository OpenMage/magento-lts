<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Centinel
 */

/**
 * Centinel validation form lookup
 *
 * @package    Mage_Centinel
 */
class Mage_Centinel_Block_Authentication_Complete extends Mage_Core_Block_Template
{
    /**
     * Prepare authentication result params and render
     *
     * @return string
     */
    protected function _toHtml()
    {
        $validator = Mage::registry('current_centinel_validator');
        if ($validator) {
            $this->setIsProcessed(true);
            $this->setIsSuccess($validator->isAuthenticateSuccessful());
        }
        return parent::_toHtml();
    }
}
