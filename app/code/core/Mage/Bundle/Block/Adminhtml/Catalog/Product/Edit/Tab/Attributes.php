<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Bundle
 */

/**
 * Bundle product attributes tab
 *
 * @package    Mage_Bundle
 *
 * @method bool getCanEditPrice()
 */
class Mage_Bundle_Block_Adminhtml_Catalog_Product_Edit_Tab_Attributes extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Attributes
{
    /**
     * Prepare attributes form of bundle product
     *
     * @return $this
     */
    #[Override]
    protected function _prepareForm()
    {
        parent::_prepareForm();

        $specialPrice = $this->getForm()->getElement('special_price');
        if ($specialPrice) {
            $specialPrice->setRenderer(
                $this->getLayout()->createBlock('bundle/adminhtml_catalog_product_edit_tab_attributes_special')
                    ->setDisableChild(false),
            );
        }

        $sku = $this->getForm()->getElement('sku');
        if ($sku) {
            $sku->setRenderer(
                $this->getLayout()->createBlock('bundle/adminhtml_catalog_product_edit_tab_attributes_extend')
                    ->setDisableChild(false),
            );
        }

        $price = $this->getForm()->getElement('price');
        if ($price) {
            $price->setRenderer(
                $this->getLayout()->createBlock(
                    'bundle/adminhtml_catalog_product_edit_tab_attributes_extend',
                    'adminhtml.catalog.product.bundle.edit.tab.attributes.price',
                )->setDisableChild(true),
            );
        }

        $tax = $this->getForm()->getElement('tax_class_id');
        if ($tax) {
            $tax->setAfterElementHtml(
                '<script type="text/javascript">'
                . "
                //<![CDATA[
                function changeTaxClassId() {
                    var priceTypeElement = document.getElementById('price_type');
                    var taxClassElement = document.getElementById('tax_class_id');
                    var taxClassAdviceElement = document.getElementById('advice-required-entry-tax_class_id');
                    if (!priceTypeElement || !taxClassElement) {
                        return;
                    }
                    if (priceTypeElement.value == '" . Mage_Bundle_Model_Product_Price::PRICE_TYPE_DYNAMIC . "') {
                        taxClassElement.disabled = true;
                        taxClassElement.value = '0';
                        taxClassElement.classList.remove('required-entry');
                        if (taxClassAdviceElement) {
                            taxClassAdviceElement.remove();
                        }
                    } else {
                        taxClassElement.disabled = false;
                        " . ($tax->getRequired() ? "taxClassElement.classList.add('required-entry');" : '') . "
                    }
                }

                document.addEventListener('DOMContentLoaded', function() {
                    var priceTypeElement = document.getElementById('price_type');
                    if (priceTypeElement) {
                        priceTypeElement.addEventListener('change', changeTaxClassId);
                        changeTaxClassId();
                    }
                });
                //]]>
                "
                . '</script>',
            );
        }

        $weight = $this->getForm()->getElement('weight');
        if ($weight) {
            $weight->setRenderer(
                $this->getLayout()->createBlock('bundle/adminhtml_catalog_product_edit_tab_attributes_extend')
                    ->setDisableChild(true),
            );
        }

        $tierPrice = $this->getForm()->getElement('tier_price');
        if ($tierPrice) {
            $tierPrice->setRenderer(
                $this->getLayout()->createBlock('adminhtml/catalog_product_edit_tab_price_tier')
                    ->setPriceColumnHeader(Mage::helper('bundle')->__('Percent Discount'))
                    ->setPriceValidation('validate-greater-than-zero validate-percents'),
            );
        }

        $groupPrice = $this->getForm()->getElement('group_price');
        if ($groupPrice) {
            $groupPrice->setRenderer(
                $this->getLayout()->createBlock('adminhtml/catalog_product_edit_tab_price_group')
                    ->setPriceColumnHeader(Mage::helper('bundle')->__('Percent Discount'))
                    ->setIsPercent(true)
                    ->setPriceValidation('validate-greater-than-zero validate-percents'),
            );
        }

        $mapEnabled = $this->getForm()->getElement('msrp_enabled');
        if ($mapEnabled && $this->getCanEditPrice() !== false) {
            $mapEnabled->setAfterElementHtml(
                '<script type="text/javascript">'
                . "
                function changePriceTypeMap() {
                    var priceTypeElement = document.getElementById('price_type');
                    var msrpEnabledElement = document.getElementById('msrp_enabled');
                    var msrpDisplayElement = document.getElementById('msrp_display_actual_price_type');
                    var msrpElement = document.getElementById('msrp');
                    if (!priceTypeElement || !msrpEnabledElement || !msrpDisplayElement || !msrpElement) {
                        return;
                    }
                    if (priceTypeElement.value == " . Mage_Bundle_Model_Product_Price::PRICE_TYPE_DYNAMIC . ') {
                        msrpEnabledElement.value = '
                        . Mage_Catalog_Model_Product_Attribute_Source_Msrp_Type_Enabled::MSRP_ENABLE_NO
                        . ';
                        msrpEnabledElement.disabled = true;
                        msrpDisplayElement.value = '
                        . Mage_Catalog_Model_Product_Attribute_Source_Msrp_Type_Price::TYPE_USE_CONFIG
                        . ";
                        msrpDisplayElement.disabled = true;
                        msrpElement.value = '';
                        msrpElement.disabled = true;
                    } else {
                        msrpEnabledElement.disabled = false;
                        msrpDisplayElement.disabled = false;
                        msrpElement.disabled = false;
                    }
                }
                document.addEventListener('DOMContentLoaded', function() {
                    var priceTypeElement = document.getElementById('price_type');
                    if (priceTypeElement) {
                        priceTypeElement.addEventListener('change', changePriceTypeMap);
                        changePriceTypeMap();
                    }
                });
                "
                . '</script>',
            );
        }

        return $this;
    }

    /**
     * Get current product from registry
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        if (!$this->getDataByKey('product')) {
            $this->setData('product', Mage::registry('product'));
        }

        return $this->getDataByKey('product');
    }
}
