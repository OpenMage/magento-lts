<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Reports
 */

/**
 * Event type model
 *
 * @package    Mage_Reports
 *
 * @method Mage_Reports_Model_Resource_Event_Type _getResource()
 * @method int getCustomerLogin()
 * @method string getEventName()
 * @method Mage_Reports_Model_Resource_Event_Type getResource()
 * @method $this setCustomerLogin(int $value)
 * @method $this setEventName(string $value)
 */
class Mage_Reports_Model_Event_Type extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('reports/event_type');
    }
}
