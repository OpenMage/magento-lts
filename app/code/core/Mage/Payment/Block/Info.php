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
 * @package    Mage_Payment
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Base payment iformation block
 *
 */
class Mage_Payment_Block_Info extends Mage_Core_Block_Template
{
    /**
     * Payment rendered specific information
     *
     * @var Varien_Object
     */
    protected $_paymentSpecificInformation = null;

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('payment/info/default.phtml');
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
     * Retrieve info model
     *
     * @return Mage_Payment_Model_Info
     */
    public function getInfo()
    {
        $info = $this->getData('info');
        if (!($info instanceof Mage_Payment_Model_Info)) {
            Mage::throwException($this->__('Can not retrieve payment info model object.'));
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

    public function toPdf()
    {
        $this->setTemplate('payment/info/pdf/default.phtml');
        return $this->toHtml();
    }

    /**
     * Prepare information specific to current payment method
     *
     * @param Varien_Object|array $transport
     * @return Varien_Object
     */
    protected function _prepareSpecificInformation($transport = null)
    {
        if (null === $this->_paymentSpecificInformation) {
            if (null === $transport) {
                $transport = new Varien_Object;
            } elseif (is_array($transport)) {
                $transport = new Varien_Object($transport);
            }
            Mage::dispatchEvent('payment_info_block_prepare_specific_information', array(
                'transport' => $transport,
                'payment'   => $this->getInfo(),
                'block'     => $this,
            ));
            $this->_paymentSpecificInformation = $transport;
        }
        return $this->_paymentSpecificInformation;
    }
}
