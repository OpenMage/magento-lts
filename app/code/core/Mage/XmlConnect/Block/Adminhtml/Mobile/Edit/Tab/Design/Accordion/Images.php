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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @copyright  Copyright (c) 2006-2019 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Tab design Accordion Images xml renderer
 *
 * @deprecated will delete in the next version
 * @category     Mage
 * @package      Mage_Xmlconnect
 * @author       Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Adminhtml_Mobile_Edit_Tab_Design_Accordion_Images
    extends Mage_XmlConnect_Block_Adminhtml_Mobile_Widget_Form
{
    /**
     * Getter for accordion item title
     *
     * @deprecated will delete in the next version
     * @return string
     */
    public function getTitle()
    {
        return $this->__('Images');
    }

    /**
     * Getter for accordion item is open flag
     *
     * @deprecated will delete in the next version
     * @return bool
     */
    public function getIsOpen()
    {
        return true;
    }

    /**
     * Prepare form
     *
     * @deprecated will delete in the next version
     * @throws Mage_Core_Exception
     * @return Mage_XmlConnect_Block_Adminhtml_Mobile_Widget_Form
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();

        $fieldset = $form->addFieldset('field_logo', array());
        $this->_addElementTypes($fieldset);
        $this->addImage($fieldset,
            'conf[native][navigationBar][icon]',
            $this->__('Logo in Header'),
            $this->__('Recommended size %spx x %spx.', 35, 35),
            $this->_getDesignPreviewImageUrl('conf/native/navigationBar/icon'),
            true
        );

        $deviceType = Mage::helper('xmlconnect')->getDeviceType();
        switch ($deviceType) {
            case Mage_XmlConnect_Helper_Data::DEVICE_TYPE_IPHONE:
                $this->addImage($fieldset,
                    'conf[native][body][bannerImage]',
                    $this->__('Banner on Home Screen'),
                    $this->__('Recommended size %spx x %spx.', 320, 230)
                        . $this->__(' Note: Image size affects the performance of your app.')
                        . $this->__('Keep your image size below %s KB for optimal performance.', 50),
                    $this->_getDesignPreviewImageUrl('conf/native/body/bannerImage'),
                    true
                );
                $this->addImage($fieldset,
                    'conf[native][body][backgroundImage]',
                    $this->__('App Background'),
                    $this->__('Recommended size %spx x %spx.', 320, 367)
                        . $this->__('Note: Image size affects the performance of your app.')
                        . $this->__('Keep your image size below %s KB for optimal performance.', 75),
                    $this->_getDesignPreviewImageUrl('conf/native/body/backgroundImage'),
                    true
                );
                break;
            case Mage_XmlConnect_Helper_Data::DEVICE_TYPE_IPAD:
                $this->addImage($fieldset,
                    'conf[native][body][bannerIpadLandscapeImage]',
                    $this->__('Banner on Home Screen (landscape mode)'),
                    $this->__('Recommended size %spx x %spx.', 1024, 344)
                        . $this->__('Note: Image size affects the performance of your app.'),
                    $this->_getDesignPreviewImageUrl('conf/native/body/bannerIpadLandscapeImage'),
                    true
                );
                $this->addImage($fieldset,
                    'conf[native][body][bannerIpadImage]',
                    $this->__('Banner on Home Screen (portrait mode)'),
                    $this->__('Recommended size %spx x %spx.', 768, 294)
                        . $this->__('Note: Image size affects the performance of your app.'),
                    $this->_getDesignPreviewImageUrl('conf/native/body/bannerIpadImage'),
                    true
                );
                $this->addImage($fieldset,
                    'conf[native][body][backgroundIpadLandscapeImage]',
                    $this->__('App Background (landscape mode)'),
                    $this->__('Recommended size %spx x %spx.', 1024, 704)
                        . $this->__('Note: Image size affects the performance of your app.'),
                    $this->_getDesignPreviewImageUrl('conf/native/body/backgroundIpadLandscapeImage'),
                    true
                );
                $this->addImage($fieldset,
                    'conf[native][body][backgroundIpadPortraitImage]',
                    $this->__('App Background (portrait mode)'),
                    $this->__('Recommended size %spx x %spx.', 768, 960)
                        . $this->__('Note: Image size affects the performance of your app.'),
                    $this->_getDesignPreviewImageUrl('conf/native/body/backgroundIpadPortraitImage'),
                    true
                );
                break;
            case Mage_XmlConnect_Helper_Data::DEVICE_TYPE_ANDROID:
                $this->addImage($fieldset,
                    'conf[native][body][bannerAndroidImage]',
                    $this->__('Banner on Home Screen'),
                    $this->__('Recommended size %spx x %spx.', 320, 258)
                        . $this->__('Note: Image size affects the performance of your app.')
                        . $this->__('Keep your image size below %s KB for optimal performance.', 50),
                    $this->_getDesignPreviewImageUrl('conf/native/body/bannerAndroidImage'),
                    true
                );
                break;
            default:
                Mage::throwException(
                    $this->__('Device doesn\'t recognized: "%s". Unable to load a helper.', $deviceType)
                );
                break;
        }

        $form->setValues(Mage::helper('xmlconnect')->getApplication()->getFormData());
        $this->setForm($form);
        return parent::_prepareForm();
    }

   /**
    * Retrieve url for images in the skin folder
    *
    * @deprecated will delete in the next version
    * @param string $name - path to file name relative to the skin dir
    * @return string
    */
    protected function _getDesignPreviewImageUrl($name)
    {
        $name = Mage::helper('xmlconnect/image')->getInterfaceImagesPaths($name);
        return Mage::helper('xmlconnect/image')->getDefaultDesignUrl($name);
    }
}
