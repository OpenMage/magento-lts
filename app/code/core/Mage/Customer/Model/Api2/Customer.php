<?php

declare(strict_types=1);

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Customer
 */
/**
 * API2 class for customer
 *
 * @package    Mage_Customer
 */
class Mage_Customer_Model_Api2_Customer extends Mage_Api2_Model_Resource
{
    /**
     * @inheritDoc
     * @throws Exception
     * @throws Mage_Core_Exception
     */
    protected function _getResourceAttributes()
    {
        return $this->getEavAttributes(Mage_Api2_Model_Auth_User_Admin::USER_TYPE != $this->getUserType(), true);
    }
}
