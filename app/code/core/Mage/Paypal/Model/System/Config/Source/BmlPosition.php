<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Paypal
 */

/**
 * Source model for available bml positions
 *
 * @category   Mage
 * @package    Mage_Paypal
 */
class Mage_Paypal_Model_System_Config_Source_BmlPosition
{
    /**
     * Bml positions source getter for Home Page
     *
     * @return array
     */
    public function getBmlPositionsHP()
    {
        return [
            '0' => Mage::helper('paypal')->__('Header (center)'),
            '1' => Mage::helper('paypal')->__('Sidebar (right)'),
        ];
    }

    /**
     * Bml positions source getter for Catalog Category Page
     *
     * @return array
     */
    public function getBmlPositionsCCP()
    {
        return [
            '0' => Mage::helper('paypal')->__('Header (center)'),
            '1' => Mage::helper('paypal')->__('Sidebar (right)'),
        ];
    }

    /**
     * Bml positions source getter for Catalog Product Page
     *
     * @return array
     */
    public function getBmlPositionsCPP()
    {
        return [
            '0' => Mage::helper('paypal')->__('Header (center)'),
            '1' => Mage::helper('paypal')->__('Near Paypal Credit checkout button'),
        ];
    }

    /**
     * Bml positions source getter for Checkout Cart Page
     *
     * @return array
     */
    public function getBmlPositionsCheckout()
    {
        return [
            '0' => Mage::helper('paypal')->__('Header (center)'),
            '1' => Mage::helper('paypal')->__('Near Paypal Credit checkout button'),
        ];
    }
}
