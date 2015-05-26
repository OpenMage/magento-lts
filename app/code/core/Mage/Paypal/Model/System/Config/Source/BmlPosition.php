<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Paypal
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Source model for available bml positions
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
        $bmlPositionsHP = array(
            '0' => Mage::helper('paypal')->__('Header (center)'),
            '1' => Mage::helper('paypal')->__('Sidebar (right)')
        );
        return $bmlPositionsHP;
    }

    /**
     * Bml positions source getter for Catalog Category Page
     *
     * @return array
     */
    public function getBmlPositionsCCP()
    {
        $bmlPositionsCCP = array(
            '0' => Mage::helper('paypal')->__('Header (center)'),
            '1' => Mage::helper('paypal')->__('Sidebar (right)')
        );
        return $bmlPositionsCCP;
    }

    /**
     * Bml positions source getter for Catalog Product Page
     *
     * @return array
     */
    public function getBmlPositionsCPP()
    {
        $bmlPositionsCPP = array(
            '0' => Mage::helper('paypal')->__('Header (center)'),
            '1' => Mage::helper('paypal')->__('Near Paypal Credit checkout button')
        );
        return $bmlPositionsCPP;
    }

    /**
     * Bml positions source getter for Checkout Cart Page
     *
     * @return array
     */
    public function getBmlPositionsCheckout()
    {
        $bmlPositionsCheckout = array(
            '0' => Mage::helper('paypal')->__('Header (center)'),
            '1' => Mage::helper('paypal')->__('Near Paypal Credit checkout button')
        );
        return $bmlPositionsCheckout;
    }
}
