<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Adminhtml store delete group block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_System_Store_Delete_Group extends Mage_Adminhtml_Block_Template
{
    public const DATA_ID = 'group_id';

    public const BUTTON_CONFIRM_DELETE  = 'confirm_deletion_button';

    protected $_template = 'system/store/delete_group.phtml';

    /**
     * @inheritDoc
     */
    protected function _prepareLayout()
    {
        $itemId = $this->getRequest()->getParam(self::DATA_ID);
        $this->setAction($this->getUrl('*/*/deleteGroupPost', ['group_id' => $itemId]));

        $this->addButtons();
        return parent::_prepareLayout();
    }

    /**
     * @codeCoverageIgnore
     */
    protected function addButtons(): void
    {
        $this->setChild(self::BUTTON_CONFIRM_DELETE, $this->getButtonConfirmDeleteBlock());
        $this->setChild(self::BUTTON_CANCEL, $this->getButtonCancelBlock());
        $this->setChild(self::BUTTON_BACK, $this->getButtonBackBlock());
    }

    public function getButtonBackBlock(): Mage_Adminhtml_Block_Widget_Button
    {
        $itemId = $this->getRequest()->getParam(self::DATA_ID);
        return parent::getButtonBlockByType(self::BUTTON_BACK)
            ->setOnClickSetLocationJsUrl('*/*/editGroup', [self::DATA_ID => $itemId])
            ->setClass(self::BUTTON__CLASS_CANCEL);
    }

    public function getButtonCancelBlock(): Mage_Adminhtml_Block_Widget_Button
    {
        $itemId = $this->getRequest()->getParam(self::DATA_ID);
        return parent::getButtonBlockByType(self::BUTTON_CANCEL)
            ->setOnClickSetLocationJsUrl('*/*/editGroup', [self::DATA_ID => $itemId]);
    }

    public function getButtonConfirmDeleteBlock(): Mage_Adminhtml_Block_Widget_Button
    {
        return parent::getButtonBlockByType(self::BUTTON_CANCEL)
            ->setLabel(Mage::helper('core')->__('Delete Store'))
            ->setOnClick('deleteForm.submit()');
    }
}
