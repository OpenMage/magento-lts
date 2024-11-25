<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Payment
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Base payment iformation block
 *
 * @category   Mage
 * @package    Mage_Payment
 *
 * @method bool hasIsSecureMode()
 */
class Mage_Payment_Block_Info extends Mage_Core_Block_Template
{
    /**
     * Payment rendered specific information
     *
     * @var Varien_Object|null
     */
    protected $_paymentSpecificInformation = null;

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('payment/info/default.phtml');
    }

    /**
     * Retrieve info model
     *
     * @return Mage_Payment_Model_Info
     */
    public function getInfo()
    {
        $info = $this->getData('info');
        if (!($info instanceof Mage_Payment_Model_Info)) {
            Mage::throwException($this->__('Cannot retrieve the payment info model object.'));
        }
        return $info;
    }

    /**
     * Retrieve payment method model
     *
     * @return Mage_Payment_Model_Method_Abstract
     */
    public function getMethod()
    {
        return $this->getInfo()->getMethodInstance();
    }

    /**
     * Render as PDF
     * @return string
     */
    public function toPdf()
    {
        $this->setTemplate('payment/info/pdf/default.phtml');
        return $this->toHtml();
    }

    /**
     * Getter for children PDF, as array. Analogue of $this->getChildHtml()
     *
     * Children must have toPdf() callable
     * Known issue: not sorted
     * @return array
     */
    public function getChildPdfAsArray()
    {
        $result = [];
        foreach ($this->getChild() as $child) {
            if (method_exists($child, 'toPdf')) {
                $result[] = $child->toPdf();
            }
        }
        return $result;
    }

    /**
     * Get some specific information in format of array($label => $value)
     *
     * @return array
     */
    public function getSpecificInformation()
    {
        return $this->_prepareSpecificInformation()->getData();
    }

    /**
     * Render the value as an array
     *
     * @param mixed $value
     * @param bool $escapeHtml
     * @return array $array
     */
    public function getValueAsArray($value, $escapeHtml = false)
    {
        if (empty($value)) {
            return [];
        }
        if (!is_array($value)) {
            $value = [$value];
        }
        if ($escapeHtml) {
            foreach ($value as $key => $val) {
                $value[$key] = $this->escapeHtml($val);
            }
        }
        return $value;
    }

    /**
     * Check whether payment information should show up in secure mode
     * true => only "public" payment information may be shown
     * false => full information may be shown
     *
     * @return bool
     */
    public function getIsSecureMode()
    {
        if ($this->hasIsSecureMode()) {
            return (bool)(int)$this->_getData('is_secure_mode');
        }
        if (!$payment = $this->getInfo()) {
            return true;
        }
        if (!$method = $payment->getMethodInstance()) {
            return true;
        }
        return !Mage::app()->getStore($method->getStore())->isAdmin();
    }

    /**
     * Prepare information specific to current payment method
     *
     * @param Varien_Object|array $transport
     * @return Varien_Object
     */
    protected function _prepareSpecificInformation($transport = null)
    {
        if ($this->_paymentSpecificInformation === null) {
            if ($transport === null) {
                $transport = new Varien_Object();
            } elseif (is_array($transport)) {
                $transport = new Varien_Object($transport);
            }
            Mage::dispatchEvent('payment_info_block_prepare_specific_information', [
                'transport' => $transport,
                'payment'   => $this->getInfo(),
                'block'     => $this,
            ]);
            $this->_paymentSpecificInformation = $transport;
        }
        return $this->_paymentSpecificInformation;
    }
}
