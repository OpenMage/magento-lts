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
 * Application submission block
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Adminhtml_Mobile_Submission
    extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Setting buttons for submit application page
     */
    public function __construct()
    {
        $this->_objectId    = 'application_id';
        $this->_controller  = 'adminhtml_mobile';
        $this->_blockGroup  = 'xmlconnect';
        $this->_mode = 'submission';
        parent::__construct();

        $this->removeButton('delete');
        $this->removeButton('save');
        $this->removeButton('reset');

        $app = Mage::helper('xmlconnect')->getApplication();
        if ($app && $app->getIsResubmitAction()) {
            $label = $this->__('Resubmit App');
        } else {
            $label = $this->__('Submit App');
        }

        $this->_addButton('submission_post', array(
            'class' => 'save',
            'label' => $label,
            'onclick' => "submitApplication()",
        ));

        $this->_updateButton('back', 'label', $this->__('Back to App Edit'));
        $this->_updateButton(
            'back',
            'onclick',
            'setLocation(\'' . $this->getUrl('*/*/edit', array('application_id' => $app->getId())) . '\')'
        );
    }

    /**
     * Adding styles to block
     *
     * @throws Mage_Core_Exception
     * @return Mage_Adminhtml_Block_Widget_Form_Container
     */
    protected function _prepareLayout()
    {
        $this->getLayout()->getBlock('head')->addJs('jscolor/jscolor.js');
        $this->getLayout()->getBlock('head')->addJs('scriptaculous/scriptaculous.js');


        $deviceType = Mage::helper('xmlconnect')->getDeviceType();
        switch ($deviceType) {
            case Mage_XmlConnect_Helper_Data::DEVICE_TYPE_IPHONE:
                $this->getLayout()->getBlock('head')->addItem('skin_css', 'xmlconnect/mobile-home.css');
                $this->getLayout()->getBlock('head')->addItem('skin_css', 'xmlconnect/mobile-catalog.css');
                break;
            case Mage_XmlConnect_Helper_Data::DEVICE_TYPE_IPAD:
                $this->getLayout()->getBlock('head')->addItem('skin_css', 'xmlconnect/mobile-ipad-home.css');
                $this->getLayout()->getBlock('head')->addItem('skin_css', 'xmlconnect/mobile-ipad-catalog.css');
                break;
            case Mage_XmlConnect_Helper_Data::DEVICE_TYPE_ANDROID:
                $this->getLayout()->getBlock('head')->addItem('skin_css', 'xmlconnect/mobile-android.css');
                break;
            default:
                Mage::throwException(
                    $this->__('Device doesn\'t recognized: "%s". Unable to load preview model.', $deviceType)
                );
                break;
        }

        return parent::_prepareLayout();
    }

    /**
     * Get form header title
     *
     * @return string
     */
    public function getHeaderText()
    {
        $app = Mage::helper('xmlconnect')->getApplication();
        if ($app && $app->getId()) {
            return $this->__('Submit App "%s"', $this->escapeHtml($app->getName()));
        }
        return '';
    }
}
