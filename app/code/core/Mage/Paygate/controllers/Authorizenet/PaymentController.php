<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paygate
 */

/**
 * Authorize Payment Controller
 *
 * @package    Mage_Paygate
 */
class Mage_Paygate_Authorizenet_PaymentController extends Mage_Core_Controller_Front_Action
{
    /**
     * Cancel active partail authorizations
     * @return void
     */
    public function cancelAction()
    {
        $result['success'] = false;
        try {
            $paymentMethod = Mage::helper('payment')->getMethodInstance(Mage_Paygate_Model_Authorizenet::METHOD_CODE);
            if ($paymentMethod) {
                $paymentMethod->cancelPartialAuthorization(Mage::getSingleton('checkout/session')->getQuote()->getPayment());
            }

            $result['success']  = true;
            $result['update_html'] = $this->_getPaymentMethodsHtml();
        } catch (Mage_Core_Exception $mageCoreException) {
            Mage::logException($mageCoreException);
            $result['error_message'] = $mageCoreException->getMessage();
        } catch (Exception $exception) {
            Mage::logException($exception);
            $result['error_message'] = $this->__('There was an error canceling transactions. Please contact us or try again later.');
        }

        Mage::getSingleton('checkout/session')->getQuote()->getPayment()->save();
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    /**
     * Get payment method step html
     *
     * @return string
     */
    protected function _getPaymentMethodsHtml()
    {
        $layout = $this->getLayout();
        $update = $layout->getUpdate();
        $update->load('checkout_onepage_paymentmethod');

        $layout->generateXml();
        $layout->generateBlocks();
        return $layout->getOutput();
    }
}
