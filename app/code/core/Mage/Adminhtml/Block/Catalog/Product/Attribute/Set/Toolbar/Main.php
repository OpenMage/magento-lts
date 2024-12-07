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
 * Adminhtml catalog product sets main page toolbar
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Catalog_Product_Attribute_Set_Toolbar_Main extends Mage_Adminhtml_Block_Template
{
    public const BUTTON_ADD = 'addButton';

    protected $_template = 'catalog/product/attribute/set/toolbar/main.phtml';

    /**
     * @codeCoverageIgnore
     * @inheritDoc
     */
    protected function _prepareLayout()
    {
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
            ->setLabel(Mage::helper('catalog')->__('Add New Set'))
            ->setOnClickSetLocationJsUrl('*/*/add');
    }

    /**
     * @return string
     * @deprecated
     * @see getAddButtonHtml()
     */
    protected function getNewButtonHtml()
    {
        return $this->getChildHtml(self::BUTTON_ADD);
    }

    /**
     * @return string
     */
    protected function _getHeader()
    {
        return Mage::helper('catalog')->__('Manage Attribute Sets');
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        Mage::dispatchEvent('adminhtml_catalog_product_attribute_set_toolbar_main_html_before', ['block' => $this]);
        return parent::_toHtml();
    }
}
