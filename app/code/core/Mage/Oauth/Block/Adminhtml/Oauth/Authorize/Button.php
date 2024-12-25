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
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * OAuth authorization block with auth buttons
 *
 * @category   Mage
 * @package    Mage_Oauth
 */
class Mage_Oauth_Block_Adminhtml_Oauth_Authorize_Button extends Mage_Oauth_Block_Authorize_ButtonBaseAbstract
{
    /**
     * Retrieve confirm authorization url path
     *
     * @return string
     */
    public function getConfirmUrlPath()
    {
        return 'adminhtml/oauth_authorize/confirm';
    }

    /**
     * Retrieve reject authorization url path
     *
     * @return string
     */
    public function getRejectUrlPath()
    {
        return 'adminhtml/oauth_authorize/reject';
    }
}
