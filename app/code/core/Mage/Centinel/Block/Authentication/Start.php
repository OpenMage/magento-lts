<?php

/**
 * @category   Mage
 * @package    Mage_Centinel
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2025 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
