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
 * @copyright  Copyright (c) 2022-2023 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * customers defined options
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Options extends Mage_Adminhtml_Block_Widget
{
    public const BLOCK_OPTION_BOX = 'options_box';

    protected $_template = 'catalog/product/edit/options.phtml';

    protected function _prepareLayout()
    {
        $this->setChild(
            self::BLOCK_OPTION_BOX,
            $this->getLayout()->createBlock('adminhtml/catalog_product_edit_tab_options_option')
        );

        $this->addButtons();
        return parent::_prepareLayout();
    }

    /**
     * @codeCoverageIgnore
     */
    protected function addButtons(): void
    {
        $this->setChild(self::BUTTON_ADD, $this->getButtonAddBlock());
    }

    public function getButtonAddBlock(): Mage_Adminhtml_Block_Widget_Button
    {
        return parent::getButtonBlockByType(self::BUTTON_ADD)
            ->setId('add_new_defined_option')
            ->setLabel(Mage::helper('catalog')->__('Add New Option'));
    }

    public function getOptionsBoxHtml()
    {
        return $this->getChildHtml(self::BLOCK_OPTION_BOX);
    }
}
