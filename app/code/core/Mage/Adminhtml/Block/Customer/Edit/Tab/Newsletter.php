<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Customer account form block
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Customer_Edit_Tab_Newsletter extends Mage_Adminhtml_Block_Widget_Form
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('customer/tab/newsletter.phtml');
    }

    public function initForm()
    {
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('_newsletter');
        $customer = $this->getRegistryCurrentCustomer();
        $subscriber = Mage::getModel('newsletter/subscriber')->loadByCustomer($customer);
        Mage::register('subscriber', $subscriber);

        if ($customer->getWebsiteId() == 0) {
            $this->setForm($form);
            return $this;
        }

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => Mage::helper('customer')->__('Newsletter Information')]);

        $fieldset->addField(
            'subscription',
            'checkbox',
            [
                'label' => Mage::helper('customer')->__('Subscribed to Newsletter?'),
                'name'  => 'subscription',
            ],
        );

        if ($customer->isReadonly()) {
            $form->getElement('subscription')->setReadonly(true, true);
        }

        $form->getElement('subscription')->setIsChecked($subscriber->isSubscribed());

        if ($changedDate = $this->getStatusChangedDate()) {
            $fieldset->addField(
                'change_status_date',
                'label',
                [
                    'label' => $subscriber->isSubscribed() ? Mage::helper('customer')->__('Last Date Subscribed') : Mage::helper('customer')->__('Last Date Unsubscribed'),
                    'value' => $changedDate,
                    'bold'  => true,
                ],
            );
        }

        $this->setForm($form);
        return $this;
    }

    public function getStatusChangedDate()
    {
        $subscriber = Mage::registry('subscriber');
        if ($subscriber->getChangeStatusAt()) {
            return $this->formatDate(
                $subscriber->getChangeStatusAt(),
                Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM,
                true,
            );
        }

        return null;
    }

    protected function _prepareLayout()
    {
        $this->setChild(
            'grid',
            $this->getLayout()->createBlock('adminhtml/customer_edit_tab_newsletter_grid', 'newsletter.grid'),
        );
        return parent::_prepareLayout();
    }

    protected function getRegistryCurrentCustomer(): ?Mage_Customer_Model_Customer
    {
        return Mage::registry('current_customer');
    }
}
