<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Paygate
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Mage
 * @package    Mage_Paygate
 * @author     Magento Core Team <core@magentocommerce.com>
 *
 * @method Mage_Paygate_Model_Resource_Authorizenet_Debug _getResource()
 * @method Mage_Paygate_Model_Resource_Authorizenet_Debug getResource()
 * @method string getRequestBody()
 * @method $this setRequestBody(string $value)
 * @method string getResponseBody()
 * @method $this setResponseBody(string $value)
 * @method string getRequestSerialized()
 * @method $this setRequestSerialized(string $value)
 * @method string getResultSerialized()
 * @method $this setResultSerialized(string $value)
 * @method string getRequestDump()
 * @method $this setRequestDump(string $value)
 * @method string getResultDump()
 * @method $this setResultDump(string $value)
 */
class Mage_Paygate_Model_Authorizenet_Debug extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('paygate/authorizenet_debug');
    }
}
