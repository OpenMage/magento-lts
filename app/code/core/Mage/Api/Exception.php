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
 * @package     Mage_Api
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Api exception
 *
 * @category   Mage
 * @package    Mage_Api
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Api_Exception extends Mage_Core_Exception
{
    protected $_customMessage = null;

    /**
     * Mage_Api_Exception constructor.
     * @param string $faultCode
     * @param string|null $customMessage
     */
    public function __construct($faultCode, $customMessage = null)
    {
        parent::__construct($faultCode);
        $this->_customMessage = $customMessage;
    }

    /**
     * Custom error message, if error is not in api.
     *
     * @return string
     */
    public function getCustomMessage()
    {
        return $this->_customMessage;
    }
}
