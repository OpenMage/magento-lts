<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Customer
 */

/**
 * Customer flow password info resource model
 *
 * @package    Mage_Customer
 */
class Mage_Customer_Model_Resource_Flowpassword extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('customer/flowpassword', 'flowpassword_id');
    }
}
