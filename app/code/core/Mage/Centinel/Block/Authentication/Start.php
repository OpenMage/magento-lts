<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 * @category   Mage
 * @package    Mage_Centinel
 */

/**
 * Authentication start/redirect form
 *
 * @category   Mage
 * @package    Mage_Centinel
 */
class Mage_Centinel_Block_Authentication_Start extends Mage_Core_Block_Template
{
    /**
     * Prepare form parameters and render
     * @return string
     */
    protected function _toHtml()
    {
        $validator = Mage::registry('current_centinel_validator');
        if ($validator && $validator->shouldAuthenticate()) {
            $this->addData($validator->getAuthenticateStartData());
            return parent::_toHtml();
        }
        return '';
    }
}
