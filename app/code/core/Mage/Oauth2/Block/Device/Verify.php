<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Oauth2
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * OAuth2 Device Verification Block
 */
class Mage_Oauth2_Block_Device_Verify extends Mage_Core_Block_Template
{
    /**
     * Constructor
     */
    protected function _construct()
    {
        $this->setTemplate('oauth2/device/verify.phtml');
    }

    /**
     * Get form action URL
     *
     * @return string
     */
    public function getFormActionUrl()
    {
        return $this->getUrl('oauth2/device/authorize');
    }

    /**
     * Get user code
     *
     * @return string|null
     */
    public function getUserCode()
    {
        return Mage::registry('current_device_code');
    }
}
