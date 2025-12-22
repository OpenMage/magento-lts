<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Checkout
 */

/**
 * Controller for onepage and multishipping checkouts
 *
 * @package    Mage_Checkout
 */
abstract class Mage_Checkout_Controller_Action extends Mage_Core_Controller_Front_Action
{
    /**
     * Make sure customer is valid, if logged in
     * By default will add error messages and redirect to customer edit form
     *
     * @param  bool $redirect  - stop dispatch and redirect?
     * @param  bool $addErrors - add error messages?
     * @return bool
     */
    protected function _preDispatchValidateCustomer($redirect = true, $addErrors = true)
    {
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        if ($customer && $customer->getId()) {
            $validationResult = $customer->validate();
            if (($validationResult !== true) && is_array($validationResult)) {
                if ($addErrors) {
                    foreach ($validationResult as $error) {
                        Mage::getSingleton('customer/session')->addError($error);
                    }
                }

                if ($redirect) {
                    $this->_redirect('customer/account/edit');
                    $this->setFlag('', self::FLAG_NO_DISPATCH, true);
                }

                return false;
            }
        }

        return true;
    }
}
