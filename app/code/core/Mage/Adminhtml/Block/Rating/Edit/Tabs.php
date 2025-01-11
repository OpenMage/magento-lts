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
 * Admin rating left menu
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Rating_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('rating_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('rating')->__('Rating Information'));
    }

    protected function _beforeToHtml()
    {
        $this->addTab('form_section', [
            'label'     => Mage::helper('rating')->__('Rating Information'),
            'title'     => Mage::helper('rating')->__('Rating Information'),
            'content'   => $this->getLayout()->createBlock('adminhtml/rating_edit_tab_form')->toHtml(),
        ])
        ;

        return parent::_beforeToHtml();
    }
}
