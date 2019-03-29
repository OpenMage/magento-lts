<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @copyright  Copyright (c) 2006-2019 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * XmlConnect error message class
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Model_Simplexml_Message_Error extends Mage_XmlConnect_Model_Simplexml_Message_Abstract
{
    /**
     * User space default error type
     */
    const ERROR_USER_SPACE_DEFAULT = 'error_1000';

    /**
     * User space account confirmation required
     */
    const ERROR_USER_SP_ACCOUNT_CONFIRMATION = 'error_1001';

    /**
     * User data validation error
     */
    const ERROR_USER_SP_DATA_VALIDATION = 'error_1002';

    /**
     * User data validation error
     */
    const ERROR_USER_SP_ACCESS_FORBIDDEN = 'error_1003';

    /**
     * Client space error default
     */
    const ERROR_CLIENT_SP_DEFAULT = 'error_2000';

    /**
     * User session is expired
     */
    const ERROR_CLIENT_SP_SESSION_EXPIRED = 'error_2001';

    /**
     * Configuration reload required
     */
    const ERROR_CLIENT_SP_CONFIG_RELOAD_REQUIRED = 'error_2002';

    /**
     * Server space default error type
     */
    const ERROR_SERVER_SP_DEFAULT = 'error_3000';

    /**
     * Undefined error type
     */
    const ERROR_UNDEFINED = 'error_0';

    /**
     * Get custom error message list
     *
     * @return array
     */
    protected function _getCustomMessageList()
    {
        return array(
            self::ERROR_CLIENT_SP_SESSION_EXPIRED => Mage::helper('xmlconnect')->__('User session is expired'),
            self::ERROR_USER_SP_ACCESS_FORBIDDEN => Mage::helper('xmlconnect')->__('Access forbidden')
        );
    }

    /**
     * Get message xml
     *
     * @return string
     */
    public function getMessage()
    {
        $this->_getXmlObject()->addCustomChild('status', $this->_getMessageStatus());
        if (!$this->_getMessageText()) {
            $this->_setMessageText($this->_getCustomMessageByCode());
        }
        $this->_getXmlObject()->addCustomChild('text', $this->_getMessageText());
        $this->_getXmlObject()->addCustomChild('error_level', substr($this->_getMessageCode(), 6));
        return $this->_getXmlObject()->asNiceXml();
    }

    /**
     * Get custom message by error code
     *
     * @return string
     */
    protected  function _getCustomMessageByCode()
    {
        $messages = $this->_getCustomMessageList();
        if (!array_key_exists($this->_getMessageCode(), $messages)){
            $this->_setMessageCode(self::ERROR_SERVER_SP_DEFAULT);
            return Mage::helper('xmlconnect')->__('Error message text is missed.');
        } else {
            return $messages[$this->_getMessageCode()];
        }
    }
}
