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
 * @category   Mage
 * @package    Mage_Oauth
 */
class Mage_Oauth_InitiateController extends Mage_Core_Controller_Front_Action
{
    /**
     * Dispatch event before action
     *
     * @inheritDoc
     */
    public function preDispatch()
    {
        $this->setFlag('', self::FLAG_NO_START_SESSION, 1);
        $this->setFlag('', self::FLAG_NO_CHECK_INSTALLATION, 1);
        $this->setFlag('', self::FLAG_NO_COOKIES_REDIRECT, 0);
        $this->setFlag('', self::FLAG_NO_PRE_DISPATCH, 1);

        return parent::preDispatch();
    }

    /**
     * Index action. Receive initiate request and response OAuth token
     */
    public function indexAction()
    {
        /** @var Mage_Oauth_Model_Server $server */
        $server = Mage::getModel('oauth/server');

        $server->initiateToken();
    }
}
