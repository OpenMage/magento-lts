<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Centinel
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Centinel Authenticate Controller
 *
 * @category   Mage
 * @package    Mage_Centinel
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Centinel_IndexController extends Mage_Core_Controller_Front_Action
{
    /**
     * Process autentication start action
     *
     */
    public function authenticationStartAction()
    {
        if ($validator = $this->_getValidator()) {
            Mage::register('current_centinel_validator', $validator);
        }
        $this->loadLayout()->renderLayout();
    }

    /**
     * Process autentication complete action
     *
     */
    public function authenticationCompleteAction()
    {
        try {
            if ($validator = $this->_getValidator()) {
                $request = $this->getRequest();

                $data = new Varien_Object();
                $data->setTransactionId($request->getParam('MD'));
                $data->setPaResPayload($request->getParam('PaRes'));

                $validator->authenticate($data);
                Mage::register('current_centinel_validator', $validator);
            }
        } catch (Exception $e) {
            Mage::register('current_centinel_validator', false);
        }
        $this->loadLayout()->renderLayout();
    }

    /**
     * Return payment model
     *
     * @return Mage_Sales_Model_Quote_Payment
     */
    private function _getPayment()
    {
        return Mage::getSingleton('checkout/session')->getQuote()->getPayment();
    }

    /**
     * Return Centinel validation model
     *
     * @return Mage_Centinel_Model_Service
     */
    private function _getValidator()
    {
        if ($this->_getPayment()->getMethodInstance()->getIsCentinelValidationEnabled()) {
            return $this->_getPayment()->getMethodInstance()->getCentinelValidator();
        }
        return false;
    }
}
