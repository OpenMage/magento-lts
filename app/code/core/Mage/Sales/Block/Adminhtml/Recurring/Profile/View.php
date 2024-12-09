<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Sales
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Recurring profile view page
 *
 * @category   Mage
 * @package    Mage_Sales
 *
 * @method string getDestElementId()
 * @method $this setViewHtml(string $value)
 */
class Mage_Sales_Block_Adminhtml_Recurring_Profile_View extends Mage_Adminhtml_Block_Widget_Container
{
    /**
     * Create buttons
     * TODO: implement ACL restrictions
     * @inheritDoc
     */
    protected function _prepareLayout()
    {
        $this->_addButton('back', [
            'label'     => Mage::helper('adminhtml')->__('Back'),
            'onclick'   => Mage::helper('core/js')->getSetLocationJs($this->getUrl('*/*/')),
            'class'     => 'back',
        ]);

        $profile = Mage::registry('current_recurring_profile');

        // cancel
        if ($profile->canCancel()) {
            $this->_addButton('cancel', [
                'label'     => Mage::helper('sales')->__('Cancel'),
                'onclick'   => Mage::helper('core/js')->getConfirmSetLocationJs(
                    $this->getUrl('*/*/updateState', ['profile' => $profile->getId(), 'action' => 'cancel'])
                ),
                'class'     => 'delete',
            ]);
        }

        // suspend
        if ($profile->canSuspend()) {
            $this->_addButton('suspend', [
                'label'     => Mage::helper('sales')->__('Suspend'),
                'onclick'   => Mage::helper('core/js')->getConfirmSetLocationJs(
                    $this->getUrl('*/*/updateState', ['profile' => $profile->getId(), 'action' => 'suspend'])
                ),
                'class'     => 'delete',
            ]);
        }

        // activate
        if ($profile->canActivate()) {
            $this->_addButton('activate', [
                'label'     => Mage::helper('sales')->__('Activate'),
                'onclick'   => Mage::helper('core/js')->getConfirmSetLocationJs(
                    $this->getUrl('*/*/updateState', ['profile' => $profile->getId(), 'action' => 'activate'])
                ),
                'class'     => 'add',
            ]);
        }

        // get update
        if ($profile->canFetchUpdate()) {
            $this->_addButton('update', [
                'label'     => Mage::helper('sales')->__('Get Update'),
                'onclick'   => Mage::helper('core/js')->getConfirmSetLocationJs(
                    $this->getUrl('*/*/updateProfile', ['profile' => $profile->getId()])
                ),
                'class'     => 'add',
            ]);
        }

        return parent::_prepareLayout();
    }

    /**
     * Set title and a hack for tabs container
     *
     * @inheritDoc
     */
    protected function _beforeToHtml()
    {
        $profile = Mage::registry('current_recurring_profile');
        $this->_headerText = Mage::helper('sales')->__('Recurring Profile # %s', $profile->getReferenceId());
        $this->setViewHtml('<div id="' . $this->getDestElementId() . '"></div>');
        return parent::_beforeToHtml();
    }
}
