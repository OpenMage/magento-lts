<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Checkout
 */

/**
 * @package    Mage_Checkout
 */
class Mage_Checkout_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        $this->_redirect('checkout/onepage', ['_secure' => true]);
    }
}
