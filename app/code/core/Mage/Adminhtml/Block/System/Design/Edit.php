<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */


/**
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_System_Design_Edit extends Mage_Adminhtml_Block_Widget
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('system/design/edit.phtml');
        $this->setId('design_edit');
    }

    /**
     * @inheritDoc
     */
    protected function _prepareLayout()
    {
        $this->setChild(
            'back_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData([
                    'label'     => Mage::helper('core')->__('Back'),
                    'onclick'   => Mage::helper('core/js')->getSetLocationJs($this->getUrl('*/*/')),
                    'class'     => 'back',
                ]),
        );

        $this->setChild(
            'save_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData([
                    'label'     => Mage::helper('core')->__('Save'),
                    'onclick'   => 'designForm.submit()',
                    'class'     => 'save',
                ]),
        );

        $this->setChild(
            'delete_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData([
                    'label'     => Mage::helper('core')->__('Delete'),
                    'onclick'   => Mage::helper('core/js')->getConfirmSetLocationJs($this->getDeleteUrl()),
                    'class'     => 'delete',
                ]),
        );
        return parent::_prepareLayout();
    }

    public function getDesignChangeId()
    {
        return Mage::registry('design')->getId();
    }

    /**
     * @return string
     */
    public function getDeleteUrl()
    {
        return $this->getUrlSecure('*/*/delete', [
            'id' => $this->getDesignChangeId(),
            Mage_Core_Model_Url::FORM_KEY => $this->getFormKey(),
        ]);
    }

    /**
     * @return string
     */
    public function getSaveUrl()
    {
        return $this->getUrl('*/*/save', ['_current' => true]);
    }

    /**
     * @return string
     */
    public function getValidationUrl()
    {
        return $this->getUrl('*/*/validate', ['_current' => true]);
    }

    /**
     * @return string
     */
    public function getHeader()
    {
        if (Mage::registry('design')->getId()) {
            return Mage::helper('core')->__('Edit Design Change');
        }
        return Mage::helper('core')->__('New Design Change');
    }
}
