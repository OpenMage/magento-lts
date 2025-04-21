<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Oauth
 */

/**
 * OAuth abstract authorization block
 *
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
