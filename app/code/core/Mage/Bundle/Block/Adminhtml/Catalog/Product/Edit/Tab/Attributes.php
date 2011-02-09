<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Bundle
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Bundle product attributes tab
 *
 * @category    Mage
 * @package     Mage_Bundle
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Bundle_Block_Adminhtml_Catalog_Product_Edit_Tab_Attributes extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Attributes
{
    protected function _prepareForm()
    {
        parent::_prepareForm();

        if ($special_price = $this->getForm()->getElement('special_price')) {
            $special_price->setRenderer(
                $this->getLayout()->createBlock('bundle/adminhtml_catalog_product_edit_tab_attributes_special')
                    ->setDisableChild(false)
            );
        }

        if ($sku = $this->getForm()->getElement('sku')) {
            $sku->setRenderer(
                $this->getLayout()->createBlock('bundle/adminhtml_catalog_product_edit_tab_attributes_extend')
                    ->setDisableChild(false)
            );
        }

        if ($price = $this->getForm()->getElement('price')) {
            $price->setRenderer(
                $this->getLayout()->createBlock('bundle/adminhtml_catalog_product_edit_tab_attributes_extend')
                    ->setDisableChild(true)
            );
        }

        if ($tax = $this->getForm()->getElement('tax_class_id')) {
            $tax->setAfterElementHtml(
                '<script type="text/javascript">'
                . "
                function changeTaxClassId() {
                    if ($('price_type').value == '" . Mage_Bundle_Model_Product_Price::PRICE_TYPE_DYNAMIC . "') {
                        $('tax_class_id').disabled = true;
                        $('tax_class_id').value = '0';
                        $('tax_class_id').removeClassName('required-entry');
                        if ($('advice-required-entry-tax_class_id')) {
                            $('advice-required-entry-tax_class_id').remove();
                        }
                    } else {
                        $('tax_class_id').disabled = false;
                        " . ($tax->getRequired() ? "$('tax_class_id').addClassName('required-entry');" : '') . "
                    }
                }

                $('price_type').observe('change', changeTaxClassId);
                changeTaxClassId();
                "
                . '</script>'
            );
        }

        if ($weight = $this->getForm()->getElement('weight')) {
            $weight->setRenderer(
                $this->getLayout()->createBlock('bundle/adminhtml_catalog_product_edit_tab_attributes_extend')
                    ->setDisableChild(true)
            );
        }

        if ($weight = $this->getForm()->getElement('tier_price')) {
            $weight->setRenderer(
                $this->getLayout()->createBlock('adminhtml/catalog_product_edit_tab_price_tier')
                    ->setPriceColumnHeader(Mage::helper('bundle')->__('Percent Discount'))
                    ->setPriceValidation('validate-greater-than-zero validate-percents')
            );
        }
    }
}
