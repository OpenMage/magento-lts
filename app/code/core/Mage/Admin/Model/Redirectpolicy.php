<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Admin
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Admin redirect policy model, guard admin from direct link to store/category/product deletion
 *
 * @category   Mage
 * @package    Mage_Admin
 */
class Mage_Admin_Model_Redirectpolicy
{
    /**
     * @var Mage_Adminhtml_Model_Url
     */
    protected $_urlModel;

    /**
     * @param array $parameters array('urlModel' => object)
     */
    public function __construct($parameters = [])
    {
        $this->_urlModel = (!empty($parameters['urlModel'])) ?
            $parameters['urlModel'] : Mage::getModel('adminhtml/url');
    }

    /**
     * Redirect to startup page after logging in if request contains any params (except security key)
     *
     * @param string|null $alternativeUrl
     * @return null|string
     */
    public function getRedirectUrl(
        Mage_Admin_Model_User $user,
        ?Zend_Controller_Request_Http $request = null,
        $alternativeUrl = null
    ) {
        if (empty($request)) {
            return null;
        }
        $countRequiredParams = ($this->_urlModel->useSecretKey()
            && $request->getParam(Mage_Adminhtml_Model_Url::SECRET_KEY_PARAM_NAME)) ? 1 : 0;
        $countGetParams = count($request->getUserParams()) + count($request->getQuery());

        return ($countGetParams > $countRequiredParams) ?
            $this->_urlModel->getUrl($user->getStartupPageUrl()) : $alternativeUrl;
    }
}
