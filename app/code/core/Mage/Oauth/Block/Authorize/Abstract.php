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
 * OAuth abstract authorization block
 *
 * @category   Mage
 * @package    Mage_Oauth
 *
 * @method string getToken()
 * @method Mage_Oauth_Block_AuthorizeBaseAbstract setToken(string $token)
 * @method bool getIsSimple()
 * @method Mage_Oauth_Block_Authorize_Button setIsSimple(bool $flag)
 * @method bool getHasException()
 * @method Mage_Oauth_Block_AuthorizeBaseAbstract setHasException(bool $flag)
 * @method null|string getVerifier()
 * @method Mage_Oauth_Block_AuthorizeBaseAbstract setVerifier(string $verifier)
 * @method bool getIsLogged()
 * @method Mage_Oauth_Block_AuthorizeBaseAbstract setIsLogged(bool $flag)
 */
abstract class Mage_Oauth_Block_Authorize_Abstract extends Mage_Core_Block_Template
{
    /**
     * Helper
     *
     * @var Mage_Oauth_Helper_Data
     */
    protected $_helper;

    /**
     * Consumer model
     *
     * @var Mage_Oauth_Model_Consumer
     */
    protected $_consumer;

    public function __construct()
    {
        parent::__construct();
        $this->_helper = Mage::helper('oauth');
    }

    /**
     * Get consumer instance by token value
     *
     * @return Mage_Oauth_Model_Consumer
     */
    public function getConsumer()
    {
        if ($this->_consumer === null) {
            /** @var Mage_Oauth_Model_Token $token */
            $token = Mage::getModel('oauth/token');
            $token->load($this->getToken(), 'token');
            $this->_consumer = $token->getConsumer();
        }
        return $this->_consumer;
    }

    /**
     * Get absolute path to template
     *
     * Load template from adminhtml/default area flag is_simple is set
     *
     * @return string
     */
    public function getTemplateFile()
    {
        if (!$this->getIsSimple()) {
            return parent::getTemplateFile();
        }

        //load base template from admin area
        $params = [
            '_relative' => true,
            '_area'     => 'adminhtml',
            '_package'  => 'default',
        ];
        return Mage::getDesign()->getTemplateFilename($this->getTemplate(), $params);
    }
}
