<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Permissions_Buttons extends Mage_Adminhtml_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('permissions/userinfo.phtml');
    }

    protected function _prepareLayout()
    {
        $this->setChild(
            'backButton',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData([
                    'label'     => Mage::helper('adminhtml')->__('Back'),
                    'onclick'   => 'window.location.href=\'' . $this->getUrl('*/*/') . '\'',
                    'class' => 'back',
                ]),
        );

        $this->setChild(
            'resetButton',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData([
                    'label'     => Mage::helper('adminhtml')->__('Reset'),
                    'onclick'   => 'window.location.reload()',
                ]),
        );

        $this->setChild(
            'saveButton',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData([
                    'label'     => Mage::helper('adminhtml')->__('Save Role'),
                    'onclick'   => 'roleForm.submit();return false;',
                    'class' => 'save',
                ]),
        );

        $this->setChild(
            'deleteButton',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData([
                    'label'     => Mage::helper('adminhtml')->__('Delete Role'),
                    'onclick'   => 'if(confirm(\'' . Mage::helper('core')->jsQuoteEscape(
                        Mage::helper('adminhtml')->__('Are you sure you want to do this?'),
                    ) . '\')) roleForm.submit(\'' . $this->getUrl('*/*/delete') . '\'); return false;',
                    'class' => 'delete',
                ]),
        );
        return parent::_prepareLayout();
    }

    public function getBackButtonHtml()
    {
        return $this->getChildHtml('backButton');
    }

    public function getResetButtonHtml()
    {
        return $this->getChildHtml('resetButton');
    }

    public function getSaveButtonHtml()
    {
        return $this->getChildHtml('saveButton');
    }

    public function getDeleteButtonHtml()
    {
        if ((int) $this->getRequest()->getParam('rid') == 0) {
            return;
        }
        return $this->getChildHtml('deleteButton');
    }

    public function getUser()
    {
        return Mage::registry('user_data');
    }
}
