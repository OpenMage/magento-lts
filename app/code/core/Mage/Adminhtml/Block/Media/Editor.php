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
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Adminhtml media library image editor
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Media_Editor extends Mage_Adminhtml_Block_Widget
{

    protected $_config;

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('media/editor.phtml');
        $this->getConfig()->setImage($this->getSkinUrl('images/image.jpg'));
        $this->getConfig()->setParams();
    }

    protected function _prepareLayout()
    {
        $this->setChild(
            'rotatecw_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->addData(array(
                    'id'      => $this->_getButtonId('rotatecw'),
                    'label'   => Mage::helper('adminhtml')->__('Rotate CW'),
                    'onclick' => $this->getJsObjectName() . '.rotateCw()'
                ))
        );

        $this->setChild(
            'rotateccw_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->addData(array(
                    'id'      => $this->_getButtonId('rotateccw'),
                    'label'   => Mage::helper('adminhtml')->__('Rotate CCW'),
                    'onclick' => $this->getJsObjectName() . '.rotateCCw()'
                ))
        );

        $this->setChild(
            'resize_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->addData(array(
                    'id'      => $this->_getButtonId('upload'),
                    'label'   => Mage::helper('adminhtml')->__('Resize'),
                    'onclick' => $this->getJsObjectName() . '.resize()'
                ))
        );

        $this->setChild(
            'image_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->addData(array(
                    'id'      => $this->_getButtonId('image'),
                    'label'   => Mage::helper('adminhtml')->__('Get Image Base64'),
                    'onclick' => $this->getJsObjectName() . '.getImage()'
                ))
        );

        return parent::_prepareLayout();
    }

    protected function _getButtonId($buttonName)
    {
        return $this->getHtmlId() . '-' . $buttonName;
    }

    public function getRotatecwButtonHtml()
    {
        return $this->getChildHtml('rotatecw_button');
    }

    public function getImageButtonHtml()
    {
        return $this->getChildHtml('image_button');
    }

    public function getRotateccwButtonHtml()
    {
        return $this->getChildHtml('rotateccw_button');
    }

    public function getResizeButtonHtml()
    {
        return $this->getChildHtml('resize_button');
    }

    /**
     * Retrive uploader js object name
     *
     * @return string
     */
    public function getJsObjectName()
    {
        return $this->getHtmlId() . 'JsObject';
    }

    /**
     * Retrive config json
     *
     * @return string
     */
    public function getConfigJson()
    {
        return Zend_Json::encode($this->getConfig()->getData());
    }

    /**
     * Retrive config object
     *
     * @return Varien_Config
     */
    public function getConfig()
    {
        if(is_null($this->_config)) {
            $this->_config = new Varien_Object();
        }

        return $this->_config;
    }

}
