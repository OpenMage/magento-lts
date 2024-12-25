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
 * admin customer left menu
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_System_Convert_Gui_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('convert_profile_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('adminhtml')->__('Import/Export Profile'));
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    protected function _beforeToHtml()
    {
        $profile = Mage::registry('current_convert_profile');

        $wizardBlock = $this->getLayout()->createBlock('adminhtml/system_convert_gui_edit_tab_wizard');
        $wizardBlock->addData($profile->getData());

        $new = !$profile->getId();

        $this->addTab('wizard', [
            'label'     => Mage::helper('adminhtml')->__('Profile Wizard'),
            'content'   => $wizardBlock->toHtml(),
            'active'    => true,
        ]);

        if (!$new) {
            if ($profile->getDirection() !== 'export') {
                $this->addTab('upload', [
                    'label'     => Mage::helper('adminhtml')->__('Upload File'),
                    'content'   => $this->getLayout()->createBlock('adminhtml/system_convert_gui_edit_tab_upload')->toHtml(),
                ]);
            }

            $this->addTab('run', [
                'label'     => Mage::helper('adminhtml')->__('Run Profile'),
                'content'   => $this->getLayout()->createBlock('adminhtml/system_convert_profile_edit_tab_run')->toHtml(),
            ]);

            /** @var Mage_Adminhtml_Block_System_Convert_Gui_Edit_Tab_View $block */
            $block = $this->getLayout()->createBlock('adminhtml/system_convert_gui_edit_tab_view');
            $this->addTab('view', [
                'label'     => Mage::helper('adminhtml')->__('Profile Actions XML'),
                'content'   => $block->initForm()->toHtml(),
            ]);

            $this->addTab('history', [
                'label'     => Mage::helper('adminhtml')->__('Profile History'),
                'content'   => $this->getLayout()->createBlock('adminhtml/system_convert_profile_edit_tab_history')->toHtml(),
            ]);
        }

        return parent::_beforeToHtml();
    }
}
