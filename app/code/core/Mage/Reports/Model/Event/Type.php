<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 * @category   Mage
 * @package    Mage_Reports
 */

/**
 * Event type model
 *
 * @category   Mage
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
