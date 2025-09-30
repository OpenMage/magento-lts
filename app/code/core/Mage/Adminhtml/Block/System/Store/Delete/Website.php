<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */


/**
 * Adminhtml store delete group block
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_System_Store_Delete_Website extends Mage_Adminhtml_Block_Template
{
    /**
     * @inheritDoc
     */
    protected function _prepareLayout()
    {
        $itemId = $this->getRequest()->getParam('website_id');

        $this->setTemplate('system/store/delete_website.phtml');
        $this->setAction($this->getUrl('*/*/deleteWebsitePost', ['website_id' => $itemId]));
        $this->setChild(
            'confirm_deletion_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData([
                    'label'     => Mage::helper('core')->__('Delete Website'),
                    'onclick'   => 'deleteForm.submit()',
                    'class'     => 'cancel',
                ]),
        );
        $onClick = Mage::helper('core/js')->getSetLocationJs($this->getUrl('*/*/editWebsite', ['website_id' => $itemId]));
        $this->setChild(
            'cancel_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData([
                    'label'     => Mage::helper('core')->__('Cancel'),
                    'onclick'   => $onClick,
                    'class'     => 'cancel',
                ]),
        );
        $this->setChild(
            'back_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData([
                    'label'     => Mage::helper('core')->__('Back'),
                    'onclick'   => $onClick,
                    'class'     => 'cancel',
                ]),
        );
        return parent::_prepareLayout();
    }
}
