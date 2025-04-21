<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Reports
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

/**
 * Event type model
 *
 * @package    Mage_Reports
 *
 * @method Mage_Reports_Model_Resource_Event_Type _getResource()
 * @method Mage_Reports_Model_Resource_Event_Type getResource()
 * @method string getEventName()
 * @method $this setEventName(string $value)
 * @method int getCustomerLogin()
 * @method $this setCustomerLogin(int $value)
 */
class Mage_Reports_Model_Event_Type extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('reports/event_type');
    }
}
