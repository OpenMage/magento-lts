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
class Mage_XmlConnect_Block_Adminhtml_Mobile_Edit_Tab_General extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * Prepare form before rendering HTML
     * Setting Form Fieldsets and fields
     *
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        $model = Mage::registry('current_app');

        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('app_');
        $fieldset = $form->addFieldset('base_fieldset', array('legend' => Mage::helper('xmlconnect')->__('App Information')));

        if ($model->getId()) {
            $fieldset->addField('application_id', 'hidden', array(
                'name' => 'application_id',
            ));
        }

        $fieldset->addField('name', 'text', array(
            'name'      => 'name',
            'label'     => Mage::helper('xmlconnect')->__('App Name'),
            'title'     => Mage::helper('xmlconnect')->__('App Name'),
            'required'  => true,
        ));

        if ($model->getId()) {
            $field = $fieldset->addField('code', 'label', array(
                'label'     => Mage::helper('xmlconnect')->__('App Code'),
                'title'     => Mage::helper('xmlconnect')->__('App Code'),
            ));
        }

        /**
         * Check is single store mode
         */
        if (!Mage::app()->isSingleStoreMode()) {
            $storeElement = $fieldset->addField('store_id', 'select', array(
                'name'      => 'store_id',
                'label'     => Mage::helper('xmlconnect')->__('Store View'),
                'title'     => Mage::helper('xmlconnect')->__('Store View'),
                'required'  => true,
                'values'    => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(true, false),
            ));
        } else {
            $storeElement = $fieldset->addField('store_id', 'hidden', array(
                'name'      => 'store_id',
                'value'     => Mage::app()->getStore(true)->getId()
            ));
            $model->setStoreId(Mage::app()->getStore(true)->getId());
        }

        if ($model->getId()) {
            $storeElement->setDisabled(true);
        }

        $fieldset->addField('type', 'select', array(
                'name'      => 'type',
                'label'     => Mage::helper('xmlconnect')->__('Device Type'),
                'title'     => Mage::helper('xmlconnect')->__('Device Type'),
                'disabled'  => $model->getId() ? true : false,
                'values'    => Mage::helper('xmlconnect')->getDeviceTypeOptions(),
        ));

        $yesNoValues = Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray();

        $fieldset->addField('browsing_mode', 'select', array(
            'label'     => Mage::helper('xmlconnect')->__('Catalog Only App?'),
            'name'      => 'browsing_mode',
            'note'      => Mage::helper('xmlconnect')->__('A Catalog Only App will not support functions such as add to cart, add to wishlist, or login.'),
            'values'   => $yesNoValues
        ));

        $form->setValues($model->getFormData());
        $this->setForm($form);
        return parent::_prepareForm();
    }

    /**
     * Tab label getter
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('xmlconnect')->__('General');
    }

    /**
     * Tab title getter
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('xmlconnect')->__('General');
    }

    /**
     * Check if tab can be shown
     *
     * @return bool
     */
    public function canShowTab()
    {
        return true;
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
