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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Recurring profile view page
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
        $this->_addButton('back', array(
            'label'     => Mage::helper('adminhtml')->__('Back'),
            'onclick'   => "setLocation('{$this->getUrl('*/*/')}')",
            'class'     => 'back',
        ));

        $profile = Mage::registry('current_recurring_profile');
        $confirmationMessage = Mage::helper('core')->jsQuoteEscape(
            Mage::helper('sales')->__('Are you sure you want to do this?')
        );

        // cancel
        if ($profile->canCancel()) {
            $url = $this->getUrl('*/*/updateState', array('profile' => $profile->getId(), 'action' => 'cancel'));
            $this->_addButton('cancel', array(
                'label'     => Mage::helper('sales')->__('Cancel'),
                'onclick'   => "confirmSetLocation('{$confirmationMessage}', '{$url}')",
                'class'     => 'delete',
            ));
        }

        // suspend
        if ($profile->canSuspend()) {
            $url = $this->getUrl('*/*/updateState', array('profile' => $profile->getId(), 'action' => 'suspend'));
            $this->_addButton('suspend', array(
                'label'     => Mage::helper('sales')->__('Suspend'),
                'onclick'   => "confirmSetLocation('{$confirmationMessage}', '{$url}')",
                'class'     => 'delete',
            ));
        }

        // activate
        if ($profile->canActivate()) {
            $url = $this->getUrl('*/*/updateState', array('profile' => $profile->getId(), 'action' => 'activate'));
            $this->_addButton('activate', array(
                'label'     => Mage::helper('sales')->__('Activate'),
                'onclick'   => "confirmSetLocation('{$confirmationMessage}', '{$url}')",
                'class'     => 'add',
            ));
        }

        // get update
        if ($profile->canFetchUpdate()) {
            $url = $this->getUrl('*/*/updateProfile', array('profile' => $profile->getId(),));
            $this->_addButton('update', array(
                'label'     => Mage::helper('sales')->__('Get Update'),
                'onclick'   => "confirmSetLocation('{$confirmationMessage}', '{$url}')",
                'class'     => 'add',
            ));
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
