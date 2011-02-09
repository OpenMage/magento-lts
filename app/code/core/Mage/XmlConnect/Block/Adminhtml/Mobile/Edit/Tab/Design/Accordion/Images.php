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
class Mage_XmlConnect_Block_Adminhtml_Mobile_Edit_Tab_Design_Accordion_Images extends Mage_XmlConnect_Block_Adminhtml_Mobile_Widget_Form
{
    /**
     * Getter for accordion item title
     *
     * @return string
     */
    public function getTitle()
    {
        return Mage::helper('xmlconnect')->__('Images');
    }

    /**
     * Getter for accordion item is open flag
     *
     * @return bool
     */
    public function getIsOpen()
    {
        return true;
    }

    /**
     * Prepare form
     *
     * @return Mage_XmlConnect_Block_Adminhtml_Mobile_Widget_Form
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();

        $fieldset = $form->addFieldset('field_logo', array());
        $this->_addElementTypes($fieldset);
        $this->addImage($fieldset,
            'conf[native][navigationBar][icon]',
            Mage::helper('xmlconnect')->__('Logo in Header'),
            Mage::helper('xmlconnect')->__('Recommended size 35px x 35px.'),
            $this->_getDesignPreviewImageUrl(Mage::helper('xmlconnect/image')->getInterfaceImagesPaths('conf/native/navigationBar/icon')),
            true
        );
        $this->addImage($fieldset,
            'conf[native][body][bannerImage]',
            Mage::helper('xmlconnect')->__('Banner on Home Screen'),
            Mage::helper('xmlconnect')->__('Recommended size 320px x 230px. Note: Image size affects the performance of your app. Keep your image size below 50 KB for optimal performance.'),
            $this->_getDesignPreviewImageUrl(Mage::helper('xmlconnect/image')->getInterfaceImagesPaths('conf/native/body/bannerImage')),
            true
        );
        $this->addImage($fieldset,
            'conf[native][body][backgroundImage]',
            Mage::helper('xmlconnect')->__('App Background'),
            Mage::helper('xmlconnect')->__('Recommended size 320px x 367px. Note: Image size affects the performance of your app. Keep your image size below 75 KB for optimal performance.'),
            $this->_getDesignPreviewImageUrl(Mage::helper('xmlconnect/image')->getInterfaceImagesPaths('conf/native/body/backgroundImage')),
            true
        );

        $form->setValues($this->getApplication()->getFormData());
        $this->setForm($form);
        return parent::_prepareForm();
    }

   /**
    * Retrieve url for images in the skin folder
    *
    * @param string $name - path to file name relative to the skin dir
    * @return string
    */
    protected function _getDesignPreviewImageUrl($name)
    {
        return Mage::helper('xmlconnect/image')->getSkinImagesUrl('design_default/' . $name);
    }
}
