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
 * @category    Mage
 * @package     Mage_Paypal
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * PayPal common payment info block
 * Uses default templates
 */
class Mage_Paypal_Block_Payment_Info extends Mage_Payment_Block_Info
{
    /**
     * Whether _addCcInfoBlock() was called
     * @var bool
     */
    private $_wasCcBlockCalled = false;

    /**
     * PayPal-specific information getter
     * @return array
     */
    public function getSpecificInformation()
    {
        $payment = $this->getInfo();
        $paypalInfo = Mage::getModel('paypal/info');
        if (Mage::app()->getStore()->isAdmin()) {
            return $paypalInfo->getPaymentInfo($payment, true);
        }
        return $paypalInfo->getPublicPaymentInfo($payment, true);
    }

    /**
     * Add cc block if needed
     *
     * @return string
     */
    public function toPdf()
    {
        $this->_addCcInfoBlock();
        return parent::toPdf();
    }

    /**
     * Add cc block if needed
     *
     * @return string
     */
    protected function _toHtml()
    {
        $this->_addCcInfoBlock();
        return parent::_toHtml();
    }

    /**
     * Instantiate & add cc block if needed
     */
    protected function _addCcInfoBlock()
    {
        if (!$this->_wasCcBlockCalled) {
            $this->_wasCcBlockCalled = true;
            $config = Mage::getModel('paypal/config', array($this->getInfo()->getMethod()));
            if ($config->doesWorkWithCc()) {
                $ccInfoBlock = Mage::getConfig()->getBlockClassName('payment/info_cc');
                $ccInfoBlock = new $ccInfoBlock;
                $this->setChild('cc_info', $ccInfoBlock->setInfo($this->getInfo()));
            }
        }
    }
}
