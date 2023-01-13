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
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customer account form block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
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
        $customer = Mage::registry('current_customer');
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
                    'name'  => 'subscription'
             ]
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
                        'bold'  => true
                 ]
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
                true
            );
        }

        return null;
    }

    protected function _prepareLayout()
    {
        $this->setChild(
            'grid',
            $this->getLayout()->createBlock('adminhtml/customer_edit_tab_newsletter_grid', 'newsletter.grid')
        );
        return parent::_prepareLayout();
    }
}
