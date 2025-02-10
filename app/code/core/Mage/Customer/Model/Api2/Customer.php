<?php
/**
 * API2 class for customer
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Customer
 */
class Mage_Customer_Model_Api2_Customer extends Mage_Api2_Model_Resource
{
    /**
     * Resource specific method to retrieve attributes' codes. May be overridden in child.
     *
     * @return array
     */
    protected function _getResourceAttributes()
    {
        return $this->getEavAttributes(Mage_Api2_Model_Auth_User_Admin::USER_TYPE != $this->getUserType(), true);
    }
}
