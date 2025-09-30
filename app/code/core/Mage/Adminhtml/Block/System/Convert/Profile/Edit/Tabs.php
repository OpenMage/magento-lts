<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * admin customer left menu
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_System_Convert_Profile_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
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
        $new = !Mage::registry('current_convert_profile')->getId();

        /** @var Mage_Adminhtml_Block_System_Convert_Profile_Edit_Tab_Edit $block */
        $block = $this->getLayout()->createBlock('adminhtml/system_convert_profile_edit_tab_edit');
        $this->addTab('edit', [
            'label'     => Mage::helper('adminhtml')->__('Profile Actions XML'),
            'content'   => $block->initForm()->toHtml(),
            'active'    => true,
        ]);

        if (!$new) {
            $this->addTab('run', [
                'label'     => Mage::helper('adminhtml')->__('Run Profile'),
                'content'   => $this->getLayout()->createBlock('adminhtml/system_convert_profile_edit_tab_run')->toHtml(),
            ]);

            $this->addTab('history', [
                'label'     => Mage::helper('adminhtml')->__('Profile History'),
                'content'   => $this->getLayout()->createBlock('adminhtml/system_convert_profile_edit_tab_history')->toHtml(),
            ]);
        }

        return parent::_beforeToHtml();
    }
}
