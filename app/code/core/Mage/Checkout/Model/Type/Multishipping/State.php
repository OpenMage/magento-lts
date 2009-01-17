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
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mage_Checkout
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Multishipping checkout state model
 *
 * @category   Mage
 * @package    Mage_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Checkout_Model_Type_Multishipping_State extends Varien_Object
{
    const STEP_SELECT_ADDRESSES = 'multishipping_addresses';
    const STEP_SHIPPING         = 'multishipping_shipping';
    const STEP_BILLING          = 'multishipping_billing';
    const STEP_OVERVIEW         = 'multishipping_overview';
    const STEP_SUCCESS          = 'multishipping_success';

    protected $_steps;
    protected $_checkout;

    public function __construct()
    {
        parent::__construct();
        $this->_steps = array(
            self::STEP_SELECT_ADDRESSES => new Varien_Object(array(
                'label' => Mage::helper('checkout')->__('Select Addresses')
            )),
            self::STEP_SHIPPING => new Varien_Object(array(
                'label' => Mage::helper('checkout')->__('Shipping Information')
            )),
            self::STEP_BILLING => new Varien_Object(array(
                'label' => Mage::helper('checkout')->__('Billing Information')
            )),
            self::STEP_OVERVIEW => new Varien_Object(array(
                'label' => Mage::helper('checkout')->__('Place Order')
            )),
            self::STEP_SUCCESS => new Varien_Object(array(
                'label' => Mage::helper('checkout')->__('Order Success')
            )),
        );

        $this->_checkout = Mage::getSingleton('checkout/type_multishipping');
        $this->_steps[$this->getActiveStep()]->setIsActive(true);
    }

    public function getCheckout()
    {
        return $this->_checkout;
    }

    /**
     * Retrieve available checkout steps
     *
     * @return array
     */
    public function getSteps()
    {
        return $this->_steps;
    }

    /**
     * Retrieve active step code
     *
     * @return string
     */
    public function getActiveStep()
    {
        $step = $this->getCheckoutSession()->getCheckoutState();
        if (isset($this->_steps[$step])) {
            return $step;
        }
        return self::STEP_SELECT_ADDRESSES;
    }

    public function setActiveStep($step)
    {
        if (isset($this->_steps[$step])) {
            $this->getCheckoutSession()->setCheckoutState($step);
        }
        else {
            $this->getCheckoutSession()->setCheckoutState(self::STEP_SELECT_ADDRESSES);
        }

        // Fix active step changing
        if(!$this->_steps[$step]->getIsActive()) {
            foreach($this->getSteps() as $stepObject) {
                $stepObject->unsIsActive();
            }
            $this->_steps[$step]->setIsActive(true);
        }
        return $this;
    }

    public function canSelectAddresses()
    {

    }

    public function canInputShipping()
    {

    }

    public function canSeeOverview()
    {

    }

    public function canSuccess()
    {

    }

    /**
     * Retrieve checkout session
     *
     * @return Mage_Checkout_Model_Session
     */
    public function getCheckoutSession()
    {
        return Mage::getSingleton('checkout/session');
    }
}
