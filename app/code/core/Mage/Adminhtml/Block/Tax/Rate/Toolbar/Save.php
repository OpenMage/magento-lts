<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Admin tax rate save toolbar
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
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
                    'class' => 'back'
                ])
        );

        $this->setChild(
            'resetButton',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData([
                    'label'     => Mage::helper('tax')->__('Reset'),
                    'onclick'   => 'window.location.reload()'
                ])
        );

        $this->setChild(
            'saveButton',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData([
                    'label'     => Mage::helper('tax')->__('Save Rate'),
                    'onclick'   => 'wigetForm.submit();return false;',
                    'class' => 'save'
                ])
        );

        $this->setChild(
            'deleteButton',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData([
                    'label'     => Mage::helper('tax')->__('Delete Rate'),
                    'onclick'   => 'deleteConfirm(\''
                        . Mage::helper('core')->jsQuoteEscape(
                            Mage::helper('tax')->__('Are you sure you want to do this?')
                        )
                        . '\', \''
                        . $this->getUrl('*/*/delete', ['rate' => $this->getRequest()->getParam('rate')])
                        . '\')',
                    'class' => 'delete'
                ])
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
     * @return string|void
     * @throws Exception
     */
    public function getDeleteButtonHtml()
    {
        if (intval($this->getRequest()->getParam('rate')) == 0) {
            return;
        }
        return $this->getChildHtml('deleteButton');
    }
}
