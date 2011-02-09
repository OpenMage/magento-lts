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
 * @package     Mage_XmlConnect
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Mage_XmlConnect_Block_Adminhtml_Mobile_Edit_Tab_Payment
    extends Mage_XmlConnect_Block_Adminhtml_Mobile_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    protected $_pages;

    /**
     * Construnctor
     * Setting view options
     */
    public function __construct()
    {
        parent::__construct();
        $this->setShowGlobalIcon(true);
    }

    /**
     * Prepare form before rendering HTML
     * Setting Form Fieldsets and fields
     *
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();

        $this->setForm($form);

        $data = $this->getApplication()->getFormData();
        $yesNoValues = Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray();

        $fieldset = $form->addFieldset('onepage_checkout', array('legend' => $this->__('Standard Checkout')));

        $fieldset->addField('conf/native/defaultCheckout/isActive', 'select', array(
            'label'     => $this->__('Enable Standard Checkout'),
            'name'      => 'conf[native][defaultCheckout][isActive]',
            'values'   => $yesNoValues,
            'note'      => $this->__('Standard Checkout uses the checkout methods provided by Magento. Only inline payment methods are supported. (e.g PayPal Direct,  Authorize.Net, etc.)'),
            'value'     => (isset($data['conf[native][defaultCheckout][isActive]']) ? $data['conf[native][defaultCheckout][isActive]'] : '1')
        ));


        /**
         * PayPal MEP management
         */
        $isExpressCheckoutAvaliable = Mage::getModel('xmlconnect/payment_method_paypal_mep')->isAvailable(null);

        $paypalActive = 0;
        if (isset($data['conf[native][paypal][isActive]'])) {
            $paypalActive = (int)($data['conf[native][paypal][isActive]'] && $isExpressCheckoutAvaliable);
        }
        $fieldsetPaypal = $form->addFieldset('paypal_mep_checkout', array('legend' => $this->__('PayPal Mobile Embedded Payment (MEP)')));

        $activateMepMethodNote = $this->__('To activate PayPal MEP payment method activate Express checkout first. ');
        $paypalConfigurationUrl = $this->escapeHtml($this->getUrl('adminhtml/system_config/edit', array('section' => 'paypal')));
        $businessAccountNote = $this->__('MEP is PayPal`s native checkout experience for the iPhone. You can choose to use MEP alongside standard checkout, or use it as your only checkout method for Magento mobile. PayPal MEP requires a <a href="%s">PayPal business account</a>', $paypalConfigurationUrl);

        $paypalActiveField = $fieldsetPaypal->addField('conf/native/paypal/isActive', 'select', array(
            'label'     => $this->__('Activate PayPal Checkout'),
            'name'      => 'conf[native][paypal][isActive]',
            'note'      => (!$isExpressCheckoutAvaliable ? $activateMepMethodNote : $businessAccountNote),
            'values'    => $yesNoValues,
            'value'     => $paypalActive,
            'disabled'  => !$isExpressCheckoutAvaliable
        ));

        $merchantlabelField = $fieldsetPaypal->addField('conf/special/merchantLabel', 'text', array(
            'name'      => 'conf[special][merchantLabel]',
            'label'     => $this->__('Merchant Label'),
            'title'     => $this->__('Merchant Label'),
            'required'  => true,
            'value'     => (isset($data['conf[special][merchantLabel]']) ? $data['conf[special][merchantLabel]'] : '')
        ));

        // field dependencies
        $this->setChild('form_after', $this->getLayout()->createBlock('adminhtml/widget_form_element_dependence')
            ->addFieldMap($merchantlabelField->getHtmlId(), $merchantlabelField->getName())
            ->addFieldMap($paypalActiveField->getHtmlId(), $paypalActiveField->getName())
            ->addFieldDependence(
                $merchantlabelField->getName(),
                $paypalActiveField->getName(),
                1)
        );

        return parent::_prepareForm();
    }

    /**
     * Tab label getter
     *
     * @return string
     */
    public function getTabLabel()
    {
        return $this->__('Payment Methods');
    }

    /**
     * Tab title getter
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->__('Payment Methods');
    }

    /**
     * Check if tab can be shown
     *
     * @return bool
     */
    public function canShowTab()
    {
        return (bool) !Mage::getSingleton('adminhtml/session')->getNewApplication();
    }

    /**
     * Check if tab hidden
     *
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }
}
