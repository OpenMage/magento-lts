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
 * @package    Mage_Sales
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Recurring profile view page
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
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
            'onclick'   => "setLocation('{$this->getUrl('*/*/')}')",
            'class'     => 'back',
        ]);

        $profile = Mage::registry('current_recurring_profile');
        $confirmationMessage = Mage::helper('core')->jsQuoteEscape(
            Mage::helper('sales')->__('Are you sure you want to do this?')
        );

        // cancel
        if ($profile->canCancel()) {
            $url = $this->getUrl('*/*/updateState', ['profile' => $profile->getId(), 'action' => 'cancel']);
            $this->_addButton('cancel', [
                'label'     => Mage::helper('sales')->__('Cancel'),
                'onclick'   => "confirmSetLocation('{$confirmationMessage}', '{$url}')",
                'class'     => 'delete',
            ]);
        }

        // suspend
        if ($profile->canSuspend()) {
            $url = $this->getUrl('*/*/updateState', ['profile' => $profile->getId(), 'action' => 'suspend']);
            $this->_addButton('suspend', [
                'label'     => Mage::helper('sales')->__('Suspend'),
                'onclick'   => "confirmSetLocation('{$confirmationMessage}', '{$url}')",
                'class'     => 'delete',
            ]);
        }

        // activate
        if ($profile->canActivate()) {
            $url = $this->getUrl('*/*/updateState', ['profile' => $profile->getId(), 'action' => 'activate']);
            $this->_addButton('activate', [
                'label'     => Mage::helper('sales')->__('Activate'),
                'onclick'   => "confirmSetLocation('{$confirmationMessage}', '{$url}')",
                'class'     => 'add',
            ]);
        }

        // get update
        if ($profile->canFetchUpdate()) {
            $url = $this->getUrl('*/*/updateProfile', ['profile' => $profile->getId(),]);
            $this->_addButton('update', [
                'label'     => Mage::helper('sales')->__('Get Update'),
                'onclick'   => "confirmSetLocation('{$confirmationMessage}', '{$url}')",
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
