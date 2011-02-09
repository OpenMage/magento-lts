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
class Mage_XmlConnect_Block_Adminhtml_Mobile_Edit_Tab_Notification extends Mage_XmlConnect_Block_Adminhtml_Mobile_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
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

        $fieldset = $form->addFieldset('notifications', array(
            'legend'    => $this->__('Urban Airship Push Notification'),
        ));

        $notificationEnabled = $fieldset->addField('conf/native/notifisations/isActive', 'select', array(
            'label'     => $this->__('Enable AirMail Message Push notification'),
            'name'      => 'conf[native][notifications][isActive]',
            'values'    => $yesNoValues,
            'value'     => (isset($data['conf[native][notifications][isActive]']) ? $data['conf[native][notifications][isActive]'] : '0'),
        ));

        $applicationKey = $fieldset->addField('conf/native/notifications/applicationKey', 'text', array(
            'label'     => $this->__('Application Key'),
            'name'      => 'conf[native][notifications][applicationKey]',
            'value'     => (isset($data['conf[native][notifications][applicationKey]']) ? $data['conf[native][notifications][applicationKey]'] : ''),
            'required'  => true
        ));

        $applicationSecret = $fieldset->addField('conf/native/notifications/applicationSecret', 'text', array(
            'label'     => $this->__('Application Secret'),
            'name'      => 'conf[native][notifications][applicationSecret]',
            'value'     => (isset($data['conf[native][notifications][applicationSecret]']) ? $data['conf[native][notifications][applicationSecret]'] : ''),
            'required'  => true
        ));


        $applicationMasterSecret = $fieldset->addField('conf/native/notifications/applicationMasterSecret', 'text', array(
            'label'     => $this->__('Application Master Secret'),
            'name'      => 'conf[native][notifications][applicationMasterSecret]',
            'value'     => (isset($data['conf[native][notifications][applicationMasterSecret]']) ? $data['conf[native][notifications][applicationMasterSecret]'] : ''),
            'required'  => true
        ));

        $mailboxTitle = $fieldset->addField('conf/native/notifications/mailboxTitle', 'text', array(
            'label'     => $this->__('Mailbox title'),
            'name'      => 'conf[native][notifications][mailboxTitle]',
            'value'     => (isset($data['conf[native][notifications][mailboxTitle]']) ? $data['conf[native][notifications][mailboxTitle]'] : ''),
            'required'  => true,
            'note'      => $this->__('The Mailbox title will be shown in the More Info tab. To understand more about the title, please <a target="_blank" href="http://www.magentocommerce.com/img/product/mobile/helpers/mail_box_title.png">click here</a>')
        ));

        // field dependencies
        $this->setChild('form_after', $this->getLayout()->createBlock('adminhtml/widget_form_element_dependence')
            ->addFieldMap($applicationKey->getHtmlId(), $applicationKey->getName())
            ->addFieldMap($applicationSecret->getHtmlId(), $applicationSecret->getName())
            ->addFieldMap($applicationMasterSecret->getHtmlId(), $applicationMasterSecret->getName())
            ->addFieldMap($mailboxTitle->getHtmlId(), $mailboxTitle->getName())
            ->addFieldMap($notificationEnabled->getHtmlId(), $notificationEnabled->getName())
            ->addFieldDependence(
                $applicationKey->getName(),
                $notificationEnabled->getName(),
                1)
            ->addFieldDependence(
                $applicationSecret->getName(),
                $notificationEnabled->getName(),
                1)
            ->addFieldDependence(
                $applicationMasterSecret->getName(),
                $notificationEnabled->getName(),
                1)
            ->addFieldDependence(
                $mailboxTitle->getName(),
                $notificationEnabled->getName(),
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
        return $this->__('Push Notification');
    }

    /**
     * Tab title getter
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->__('Push Notification');
    }

    /**
     * Check if tab can be shown
     *
     * @return bool
     */
    public function canShowTab()
    {
        return (bool) !Mage::getSingleton('adminhtml/session')->getNewApplication() 
            && $this->getApplication()->getType() == Mage_XmlConnect_Helper_Data::DEVICE_TYPE_IPHONE;
    }

    /**
     * Check if tab hidden
     *
     * @return bool
     */
    public function isHidden()
    {
        if (!$this->getData('conf/special/notifications_submitted')) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Append helper above form
     *
     * @return string
     */
    protected function _toHtml()
    {
        return $this->getChildHtml('app_notification_helper') . parent::_toHtml();
    }
}
