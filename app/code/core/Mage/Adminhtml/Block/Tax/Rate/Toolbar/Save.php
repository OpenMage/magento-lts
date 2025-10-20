<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */


/**
 * Admin tax rate save toolbar
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Tax_Rate_Toolbar_Save extends Mage_Adminhtml_Block_Template
{
    /**
     * Mage_Adminhtml_Block_Tax_Rate_Toolbar_Save constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->assign('createUrl', $this->getUrl('*/tax_rate/save'));
        $this->setTemplate('tax/toolbar/rate/save.phtml');
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    protected function _prepareLayout()
    {
        $this->setChild(
            'backButton',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData([
                    'label'     => Mage::helper('tax')->__('Back'),
                    'onclick'   => 'window.location.href=\'' . $this->getUrl('*/*/') . '\'',
                    'class'     => 'back',
                ]),
        );

        $this->setChild(
            'resetButton',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData([
                    'label'     => Mage::helper('tax')->__('Reset'),
                    'onclick'   => 'window.location.reload()',
                    'class'     => 'reset',
                ]),
        );

        $this->setChild(
            'saveButton',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData([
                    'label'     => Mage::helper('tax')->__('Save Rate'),
                    'onclick'   => 'wigetForm.submit();return false;',
                    'class'     => 'save',
                ]),
        );

        $this->setChild(
            'deleteButton',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData([
                    'label'     => Mage::helper('tax')->__('Delete Rate'),
                    'onclick'   => Mage::helper('core/js')->getDeleteConfirmJs(
                        $this->getUrl('*/*/delete', ['rate' => $this->getRequest()->getParam('rate')]),
                    ),
                    'class'     => 'delete',
                ]),
        );
        return parent::_prepareLayout();
    }

    /**
     * @return string
     */
    public function getBackButtonHtml()
    {
        return $this->getChildHtml('backButton');
    }

    /**
     * @return string
     */
    public function getResetButtonHtml()
    {
        return $this->getChildHtml('resetButton');
    }

    /**
     * @return string
     */
    public function getSaveButtonHtml()
    {
        return $this->getChildHtml('saveButton');
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getDeleteButtonHtml()
    {
        if ((int) $this->getRequest()->getParam('rate') == 0) {
            return '';
        }

        return $this->getChildHtml('deleteButton');
    }
}
