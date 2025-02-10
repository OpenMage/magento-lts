<?php
/**
 * Shopping cart api
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Checkout
 */
class Mage_Checkout_Model_Cart_Payment_Api_V2 extends Mage_Checkout_Model_Cart_Payment_Api
{
    /**
      * @param object $data
      * @return array
      */
    protected function _preparePaymentData($data)
    {
        if (($_data = get_object_vars($data)) !== null) {
            return parent::_preparePaymentData($_data);
        }

        return [];
    }
}
