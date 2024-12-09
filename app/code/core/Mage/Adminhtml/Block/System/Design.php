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
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_System_Design extends Mage_Adminhtml_Block_Template
{
    /**
     * @inheritDoc
     */
    protected function _prepareLayout()
    {
        $this->setTemplate('system/design/index.phtml');

        $this->setChild(
            'add_new_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData([
                    'label'     => Mage::helper('catalog')->__('Add Design Change'),
                    'onclick'   => Mage::helper('core/js')->getSetLocationJs($this->getUrl('*/*/new')),
                    'class'     => 'add',
                ]),
        );

        $this->setChild('grid', $this->getLayout()->createBlock('adminhtml/system_design_grid', 'design.grid'));
        return parent::_prepareLayout();
    }
}
