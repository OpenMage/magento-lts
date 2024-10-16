<?php
/**
 * OpenMage
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
 * @category    Mage
 * @package     Mage_Eav
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mage_Eav_Block_Adminhtml_Attribute_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('eav_attribute_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('eav')->__('Attribute Information'));
    }

    protected function _beforeToHtml()
    {
        $this->addTab('main', [
            'label'     => Mage::helper('eav')->__('Properties'),
            'title'     => Mage::helper('eav')->__('Properties'),
            'content'   => $this->getLayout()->createBlock('eav/adminhtml_attribute_edit_tab_main')->toHtml(),
            'active'    => true
        ]);

        $model = Mage::registry('entity_attribute');

        $this->addTab('labels', [
            'label'     => Mage::helper('eav')->__('Manage Label / Options'),
            'title'     => Mage::helper('eav')->__('Manage Label / Options'),
            'content'   => $this->getLayout()->createBlock('eav/adminhtml_attribute_edit_tab_options')->toHtml(),
        ]);

        return parent::_beforeToHtml();
    }
}
