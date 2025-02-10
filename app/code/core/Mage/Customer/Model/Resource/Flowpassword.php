<?php
/**
 * Customer flow password info resource model
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Customer
 */
class Mage_Customer_Model_Resource_Flowpassword extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('customer/flowpassword', 'flowpassword_id');
    }
}
