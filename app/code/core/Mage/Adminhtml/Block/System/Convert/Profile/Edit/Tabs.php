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
 * admin customer left menu
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
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
