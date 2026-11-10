<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */


/**
 * Recurring profile view page
 *
 * @package    Mage_Sales
 *
 * @method string getDestElementId()
 * @method $this  setViewHtml(string $value)
 */
class Mage_Sales_Block_Adminhtml_Recurring_Profile_View extends Mage_Adminhtml_Block_Widget_Container
{
    /**
     * Create buttons
     * TODO: implement ACL restrictions
     * @inheritDoc
     */
    #[Override]
    protected function _prepareLayout()
    {
        $this->_addPreparedButton(id: self::BUTTON_TYPE_BACK);

        $profile = Mage::registry('current_recurring_profile');

        // cancel
        if ($profile->canCancel()) {
            $this->_addPreparedButton(
                id: self::BUTTON_TYPE_CANCEL,
                module: 'sales',
                onClick: Mage::helper('core/js')->getConfirmSetLocationJs(
                    $this->getUrl('*/*/updateState', ['profile' => $profile->getId(), 'action' => 'cancel']),
                ),
            );
        }

        // suspend
        if ($profile->canSuspend()) {
            $onClick = Mage::helper('core/js')->getConfirmSetLocationJs(
                $this->getUrl('*/*/updateState', ['profile' => $profile->getId(), 'action' => 'suspend']),
            );

            $this->_addPreparedButton(
                id: 'suspend',
                label: Mage::helper('sales')->__('Suspend'),
                class: 'delete',
                onClick: $onClick,
            );
        }

        // activate
        if ($profile->canActivate()) {
            $onClick = Mage::helper('core/js')->getConfirmSetLocationJs(
                $this->getUrl('*/*/updateState', ['profile' => $profile->getId(), 'action' => 'activate']),
            );

            $this->_addPreparedButton(
                id: 'activate',
                label: Mage::helper('sales')->__('Activate'),
                class: 'add',
                onClick: $onClick,
            );
        }

        // get update
        if ($profile->canFetchUpdate()) {
            $onClick = Mage::helper('core/js')->getConfirmSetLocationJs(
                $this->getUrl('*/*/updateProfile', ['profile' => $profile->getId()]),
            );

            $this->_addPreparedButton(
                id: 'update',
                label: Mage::helper('sales')->__('Get Update'),
                class: 'add',
                onClick: $onClick,
            );
        }

        return parent::_prepareLayout();
    }

    /**
     * Set title and a hack for tabs container
     *
     * @inheritDoc
     */
    #[Override]
    protected function _beforeToHtml()
    {
        $profile = Mage::registry('current_recurring_profile');
        $this->_headerText = Mage::helper('sales')->__('Recurring Profile # %s', $profile->getReferenceId());
        $this->setViewHtml('<div id="' . $this->getDestElementId() . '"></div>');
        return parent::_beforeToHtml();
    }
}
