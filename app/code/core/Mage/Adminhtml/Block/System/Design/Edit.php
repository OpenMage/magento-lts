<?php
/**
 * OpenMage
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
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_System_Design_Edit extends Mage_Adminhtml_Block_Widget
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('system/design/edit.phtml');
        $this->setId('design_edit');
    }

    protected function _prepareLayout()
    {
        $this->setChild('back_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData([
                    'label'     => Mage::helper('core')->__('Back'),
                    'onclick'   => 'setLocation(\''.$this->getUrl('*/*/').'\')',
                    'class' => 'back'
                ])
        );

        $this->setChild('save_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData([
                    'label'     => Mage::helper('core')->__('Save'),
                    'onclick'   => 'designForm.submit()',
                    'class' => 'save'
                ])
        );

        $confirmationMessage = Mage::helper('core')->jsQuoteEscape(
            Mage::helper('core')->__('Are you sure?')
        );
        $this->setChild('delete_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData([
                    'label'     => Mage::helper('core')->__('Delete'),
                    'onclick'   => 'confirmSetLocation(\'' . $confirmationMessage . '\', \'' . $this->getDeleteUrl()
                        . '\')',
                    'class'  => 'delete'
                ])
        );
        return parent::_prepareLayout();
    }

    public function getDesignChangeId()
    {
        return Mage::registry('design')->getId();
    }

    public function getDeleteUrl()
    {
        return $this->getUrlSecure('*/*/delete', [
            'id' => $this->getDesignChangeId(),
            Mage_Core_Model_Url::FORM_KEY => $this->getFormKey()
        ]);
    }

    public function getSaveUrl()
    {
        return $this->getUrl('*/*/save', ['_current'=>true]);
    }

    public function getValidationUrl()
    {
        return $this->getUrl('*/*/validate', ['_current'=>true]);
    }

    public function getHeader()
    {
        $header = '';
        if (Mage::registry('design')->getId()) {
            $header = Mage::helper('core')->__('Edit Design Change');
        } else {
            $header = Mage::helper('core')->__('New Design Change');
        }
        return $header;
    }
}
