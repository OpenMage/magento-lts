<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Oauth
 */

/**
 * oAuth token controller
 *
 * @category   Mage
 * @package    Mage_Oauth
 */
class Mage_Oauth_TokenController extends Mage_Core_Controller_Front_Action
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
     * Index action. Process request and response permanent token
     */
    public function indexAction()
    {
        /** @var Mage_Oauth_Model_Server $server */
        $server = Mage::getModel('oauth/server');

        $server->accessToken();
    }
}
