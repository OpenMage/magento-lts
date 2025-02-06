<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml system templates page content block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_System_Email_Template extends Mage_Adminhtml_Block_Template
{
    /**
     * Set transactional emails grid template
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('system/email/template/list.phtml');
    }

    /**
     * @inheritDoc
     */
    protected function _prepareLayout()
    {
        $this->setChild(
            'add_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData([
                    'label'     => Mage::helper('adminhtml')->__('Add New Template'),
                    'onclick'   => "window.location='" . $this->getCreateUrl() . "'",
                    'class'     => 'add',
                ]),
        );
        $this->setChild('grid', $this->getLayout()->createBlock('adminhtml/system_email_template_grid', 'email.template.grid'));
        return parent::_prepareLayout();
    }

    /**
     * Get URL for create new email template
     *
     * @return string
     */
    public function getCreateUrl()
    {
        return $this->getUrl('*/*/new');
    }

    /**
     * Get transactional emails page header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        return Mage::helper('adminhtml')->__('Transactional Emails');
    }

    /**
     * Get Add New Template button html
     *
     * @return string
     */
    protected function getAddButtonHtml()
    {
        return $this->getChildHtml('add_button');
    }
}
