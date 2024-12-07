<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Oauth
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * OAuth authorization block
 *
 * @category   Mage
 * @package    Mage_Oauth
 */
class Mage_Oauth_Block_Authorize extends Mage_Oauth_Block_AuthorizeBaseAbstract
{
    /**
     * Retrieve customer form posting url
     *
     * @return string
     */
    public function getPostActionUrl()
    {
        /** @var Mage_Customer_Helper_Data $helper */
        $helper = $this->helper('customer');
        $url = $helper->getLoginPostUrl();
        if ($this->getIsSimple()) {
            if (strstr($url, '?')) {
                $url .= '&simple=1';
            } else {
                $url = rtrim($url, '/') . '/simple/1';
            }
        }
        return $url;
    }

    /**
     * Get form identity label
     *
     * @return string
     */
    public function getIdentityLabel()
    {
        return $this->__('Email Address');
    }

    /**
     * Get form identity label
     *
     * @return string
     */
    public function getFormTitle()
    {
        return $this->__('Log in as customer');
    }

    /**
     * Retrieve reject URL path
     *
     * @return string
     */
    public function getRejectUrlPath()
    {
        return 'oauth/authorize/reject';
    }
}
