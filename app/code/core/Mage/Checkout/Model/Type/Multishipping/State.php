<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Checkout
 */

/**
 * Multishipping checkout state model
 *
 * @package    Mage_Checkout
 */
class Mage_Checkout_Model_Type_Multishipping_State extends Varien_Object
{
    public const STEP_SELECT_ADDRESSES = 'multishipping_addresses';
    public const STEP_SHIPPING         = 'multishipping_shipping';
    public const STEP_BILLING          = 'multishipping_billing';
    public const STEP_OVERVIEW         = 'multishipping_overview';
    public const STEP_SUCCESS          = 'multishipping_success';

    /**
     * Allow steps array
     *
     * @var array
     */
    protected $_steps;

    /**
     * Checkout model
     *
     * @var Mage_Checkout_Model_Type_Multishipping
     */
    protected $_checkout;

    /**
     * Init model, steps
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->_steps = [
            self::STEP_SELECT_ADDRESSES => new Varien_Object([
                'label' => Mage::helper('checkout')->__('Select Addresses'),
            ]),
            self::STEP_SHIPPING => new Varien_Object([
                'label' => Mage::helper('checkout')->__('Shipping Information'),
            ]),
            self::STEP_BILLING => new Varien_Object([
                'label' => Mage::helper('checkout')->__('Billing Information'),
            ]),
            self::STEP_OVERVIEW => new Varien_Object([
                'label' => Mage::helper('checkout')->__('Place Order'),
            ]),
            self::STEP_SUCCESS => new Varien_Object([
                'label' => Mage::helper('checkout')->__('Order Success'),
            ]),
        ];

        foreach ($this->_steps as $step) {
            $step->setIsComplete(false);
        }

        $this->_checkout = Mage::getSingleton('checkout/type_multishipping');
        $this->_steps[$this->getActiveStep()]->setIsActive(true);
    }

    /**
     * Retrieve checkout model
     *
     * @return Mage_Checkout_Model_Type_Multishipping
     */
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

    /**
     * @param string $step
     * @return $this
     */
    public function setActiveStep($step)
    {
        if (isset($this->_steps[$step])) {
            $this->getCheckoutSession()->setCheckoutState($step);
        } else {
            $this->getCheckoutSession()->setCheckoutState(self::STEP_SELECT_ADDRESSES);
        }

        // Fix active step changing
        if (!$this->_steps[$step]->getIsActive()) {
            foreach ($this->getSteps() as $stepObject) {
                $stepObject->unsIsActive();
            }
            $this->_steps[$step]->setIsActive(true);
        }
        return $this;
    }

    /**
     * Mark step as completed
     *
     * @param string $step
     * @return $this
     */
    public function setCompleteStep($step)
    {
        if (isset($this->_steps[$step])) {
            $this->getCheckoutSession()->setStepData($step, 'is_complete', true);
        }
        return $this;
    }

    /**
     * Retrieve step complete status
     *
     * @param string $step
     * @return bool
     */
    public function getCompleteStep($step)
    {
        if (isset($this->_steps[$step])) {
            return $this->getCheckoutSession()->getStepData($step, 'is_complete');
        }
        return false;
    }

    /**
     * Unset complete status from step
     *
     * @param string $step
     * @return $this
     */
    public function unsCompleteStep($step)
    {
        if (isset($this->_steps[$step])) {
            $this->getCheckoutSession()->setStepData($step, 'is_complete', false);
        }
        return $this;
    }

    public function canSelectAddresses() {}

    public function canInputShipping() {}

    public function canSeeOverview() {}

    public function canSuccess() {}

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
