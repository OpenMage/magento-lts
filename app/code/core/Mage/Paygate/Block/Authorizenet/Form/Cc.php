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
 * @package    Mage_Paygate
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2017-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Mage
 * @package    Mage_Paygate
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Paygate_Block_Authorizenet_Form_Cc extends Mage_Payment_Block_Form
{
    /**
     * Set block template
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('paygate/form/cc.phtml');
    }

    /**
     * Retrieve payment method form html
     *
     * @return string
     */
    public function getMethodFormBlock()
    {
        return $this->getLayout()->createBlock('payment/form_cc')
            ->setMethod($this->getMethod());
    }

    /**
     * Cards info block
     *
     * @return string
     */
    public function getCardsBlock()
    {
        return $this->getLayout()->createBlock('paygate/authorizenet_info_cc')
            ->setMethod($this->getMethod())
            ->setInfo($this->getMethod()->getInfoInstance())
            ->setCheckoutProgressBlock(false)
            ->setHideTitle(true);
    }

    /**
     * Return url to cancel controller
     *
     * @return string
     */
    public function getCancelUrl()
    {
        return $this->getUrl('paygate/authorizenet_payment/cancel');
    }

    /**
     * Return url to admin cancel controller from admin url model
     *
     * @return string
     */
    public function getAdminCancelUrl()
    {
        return Mage::getModel('adminhtml/url')->getUrl('adminhtml/paygate_authorizenet_payment/cancel');
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        $this->setChild('cards', $this->getCardsBlock());
        $this->setChild('method_form_block', $this->getMethodFormBlock());
        return parent::_toHtml();
    }

    /**
     * Get notice message
     *
     * @return string
     */
    public function showNoticeMessage($message)
    {
        return $this->getLayout()->getMessagesBlock()
            ->addNotice($this->__($message))
            ->getGroupedHtml();
    }

    /**
     * Return partial authorization confirmation message and unset it in payment model
     *
     * @return string|false
     */
    public function getPartialAuthorizationConfirmationMessage()
    {
        $lastActionState = $this->getMethod()->getPartialAuthorizationLastActionState();
        if ($lastActionState == Mage_Paygate_Model_Authorizenet::PARTIAL_AUTH_LAST_SUCCESS) {
            $this->getMethod()->unsetPartialAuthorizationLastActionState();
            return Mage::helper('paygate')->__('The amount on your credit card is insufficient to complete your purchase. The available amount has been put on hold. To complete your purchase click OK and specify additional credit card number. To cancel the purchase and release the amount on hold, click Cancel.');
        } elseif ($lastActionState == Mage_Paygate_Model_Authorizenet::PARTIAL_AUTH_LAST_DECLINED) {
            $this->getMethod()->unsetPartialAuthorizationLastActionState();
            return Mage::helper('paygate')->__('Your credit card has been declined. Click OK to specify another credit card to complete your purchase. Click Cancel to release the amount on hold and select another payment method.');
        }
        return false;
    }

    /**
     * Return partial authorization form message and unset it in payment model
     *
     * @return string
     */
    public function getPartialAuthorizationFormMessage()
    {
        $lastActionState = $this->getMethod()->getPartialAuthorizationLastActionState();
        $message = false;
        switch ($lastActionState) {
            case Mage_Paygate_Model_Authorizenet::PARTIAL_AUTH_ALL_CANCELED:
                $message = Mage::helper('paygate')->__('Your payment has been cancelled. All authorized amounts have been released.');
                break;
            case Mage_Paygate_Model_Authorizenet::PARTIAL_AUTH_CARDS_LIMIT_EXCEEDED:
                $message = Mage::helper('paygate')->__('You have reached the maximum number of credit cards that can be used for one payment. The available amounts on all used cards were insufficient to complete payment. The payment has been cancelled and amounts on hold have been released.');
                break;
            case Mage_Paygate_Model_Authorizenet::PARTIAL_AUTH_DATA_CHANGED:
                $message = Mage::helper('paygate')->__('Your order has not been placed, because contents of the shopping cart and/or address has been changed. Authorized amounts from your previous payment that were left pending are now released. Please go through the checkout process for your recent cart contents.');
                break;
        }
        if ($message) {
            $this->getMethod()->unsetPartialAuthorizationLastActionState();
        }
        return $message;
    }

    /**
     * Return cancel confirmation message
     *
     * @return string
     */
    public function getCancelConfirmationMessage()
    {
        return $this->__('Are you sure you want to cancel your payment? Click OK to cancel your payment and release the amount on hold. Click Cancel to enter another credit card and continue with your payment.');
    }

    /**
     * Return flag - is partial authorization process started
     *
     * @return string
     */
    public function isPartialAuthorization()
    {
        return $this->getMethod()->isPartialAuthorization();
    }

    /**
     * Return HTML content for creating admin panel`s button
     *
     * @return string
     */
    public function getCancelButtonHtml()
    {
        $cancelButton = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData([
                'id'      => 'payment_cancel',
                'label'   => Mage::helper('paygate')->__('Cancel'),
                'onclick' => 'cancelPaymentAuthorizations()'
            ]);
        return $cancelButton->toHtml();
    }
}
