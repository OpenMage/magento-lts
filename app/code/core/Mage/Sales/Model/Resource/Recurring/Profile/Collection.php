<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/**
 * Recurring profile collection
 *
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Resource_Recurring_Profile_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * @var string
     */
    protected $_eventPrefix    = 'sales_recurring_profile_collection';

    /**
     * @var string
     */
    protected $_eventObject    = 'recurring_profile_collection';

    /**
     * Entity initialization
     *
     */
    protected function _construct()
    {
        $this->_init('sales/recurring_profile');
    }
}
