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
 * Catalog price rules
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Promo_Catalog extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_addButton('apply_rules', [
            'label'     => Mage::helper('catalogrule')->__('Apply Rules'),
            'onclick'   => "location.href='" . $this->getUrl('*/*/applyRules') . "'",
            'class'     => '',
        ]);

        $this->_controller = 'promo_catalog';
        $this->_headerText = Mage::helper('catalogrule')->__('Catalog Price Rules');
        $this->_addButtonLabel = Mage::helper('catalogrule')->__('Add New Rule');
        parent::__construct();
    }
}
