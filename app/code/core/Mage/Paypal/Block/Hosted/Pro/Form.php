<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Paypal
 */

/**
 * Hosted Pro link form
 *
 * @category   Mage
 * @package    Mage_Paypal
 */
class Mage_Paypal_Block_Hosted_Pro_Form extends Mage_Payment_Block_Form
{
    /**
     * Set info template for payment step
     *
    */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('paypal/hss/info.phtml');
    }
}
