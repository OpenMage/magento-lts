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
 * @package     Mage_XmlConnect
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customer order item xml renderer for bundle product type
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Customer_Order_Item_Renderer_Bundle
    extends Mage_Bundle_Block_Sales_Order_Items_Renderer
{
    /**
     * Add item to XML object
     *
     * @param Mage_XmlConnect_Model_Simplexml_Element $orderItemXmlObj
     * @return void
     */
    public function addItemToXmlObject(Mage_XmlConnect_Model_Simplexml_Element $orderItemXmlObj)
    {
        /** @var $parentItem Mage_Sales_Model_Order_Item */
        $parentItem     = $this->getItem();

        $items          = array_merge(array($parentItem), $parentItem->getChildrenItems());
        $_prevOptionId  = '';

        /** @var $weeeHelper Mage_Weee_Helper_Data */
        $weeeHelper = $this->helper('weee');
        /** @var $taxHelper Mage_Tax_Helper_Data */
        $taxHelper  = $this->helper('tax');

        /** @var $itemXml Mage_XmlConnect_Model_Simplexml_Element */
        $itemXml    = $orderItemXmlObj->addChild('item');
        /** @var $optionsXml Mage_XmlConnect_Model_Simplexml_Element */
        $optionsXml = $itemXml->addChild('related_products');

        $weeeTaxAppliedAmount   = (float)$parentItem->getWeeeTaxAppliedAmount();
        $weeeTaxDisposition     = (float)$parentItem->getWeeeTaxDisposition();
        $typeOfDisplay1     = $weeeHelper->typeOfDisplay($parentItem, 1, 'sales')
            && $weeeTaxAppliedAmount;
        $typeOfDisplay2     = $weeeHelper->typeOfDisplay($parentItem, 2, 'sales')
            && $weeeTaxAppliedAmount;
        $typeOfDisplay4     = $weeeHelper->typeOfDisplay($parentItem, 4, 'sales')
            && $weeeTaxAppliedAmount;
        $typeOfDisplay014   = $weeeHelper->typeOfDisplay($parentItem, array(0, 1, 4), 'sales')
            && $weeeTaxAppliedAmount;
        $weeeTaxes = $weeeHelper->getApplied($parentItem);

        /** @var $_item Mage_Sales_Model_Order_Item */
        foreach ($items as $_item) {
            $isOption = $_item->getParentItem() ? true : false;

            /** @var $objectXml Mage_XmlConnect_Model_Simplexml_Element */
            if ($isOption) {
                $objectXml = $optionsXml->addChild('item');
            } else {
                $objectXml = $itemXml;
            }
            $objectXml->addAttribute('product_id', $_item->getProductId());
            $objectXml->addCustomChild('entity_type', $_item->getProductType());

            if ($isOption) {
                $attributes = $this->getSelectionAttributes($_item);
                if ($_prevOptionId != $attributes['option_id']) {
                    $objectXml->addAttribute('label', $objectXml->xmlAttribute($attributes['option_label']));
                    $_prevOptionId = $attributes['option_id'];
                }
            }

            $objectXml->addCustomChild('sku', Mage::helper('core/string')->splitInjection($_item->getSku()));

            if ($isOption) {
                $name = $this->getValueHtml($_item);
            } else {
                $name = $_item->getName();
            }
            $objectXml->addCustomChild('name', $name);

            // set prices exactly for the Bundle product, but not for related products
            if (!$isOption) {
                /** @var $priceXml Mage_XmlConnect_Model_Simplexml_Element */
                $priceXml = $objectXml->addChild('price');

                // Price excluding tax
                if ($taxHelper->displaySalesBothPrices() || $taxHelper->displaySalesPriceExclTax()) {

                    if ($typeOfDisplay014) {
                        $price = $parentItem->getPrice() + $weeeTaxAppliedAmount + $weeeTaxDisposition;
                    } else {
                        $price = $parentItem->getPrice();
                    }

                    $config = array(
                        'value' => $this->_formatPrice($price)
                    );
                    if ($taxHelper->displaySalesBothPrices()) {
                        $config['label'] = $this->__('Excl. Tax');
                    }
                    $exclPriceXml = $priceXml->addCustomChild(
                        'excluding_tax',
                        null,
                        $config
                    );

                    // TODO: move repeated code into another place
                    if ($weeeTaxes) {
                        /** @var $weeeXml Mage_XmlConnect_Model_Simplexml_Element */
                        if ($typeOfDisplay1) {
                            $weeeXml = $exclPriceXml->addChild('weee');
                            foreach ($weeeTaxes as $tax) {
                                $weeeXml->addCustomChild(
                                    'tax',
                                    $this->_formatPrice($tax['amount']),
                                    array('label' => $tax['title'])
                                );
                            }
                        } elseif ($typeOfDisplay2 || $typeOfDisplay4) {
                            $weeeXml = $exclPriceXml->addChild('weee');
                            foreach ($weeeTaxes as $tax) {
                                $weeeXml->addCustomChild(
                                    'tax',
                                    $this->_formatPrice($tax['amount_incl_tax']),
                                    array('label' => $tax['title'])
                                );
                            }
                        }

                        if ($typeOfDisplay2) {
                            if (!isset($weeeXml)) {
                                $weeeXml = $exclPriceXml->addChild('weee');
                            }
                            $weeeXml->addCustomChild(
                                'total',
                                $this->_formatPrice(
                                    $parentItem->getPrice() + $weeeTaxAppliedAmount + $weeeTaxDisposition
                                ),
                                array('label' => $weeeHelper->__('Total'))
                            );
                        }
                        if (isset($weeeXml)) {
                            unset($weeeXml);
                        }
                    }
                }

                // Price including tax
                if ($taxHelper->displaySalesBothPrices() || $taxHelper->displaySalesPriceInclTax()) {
                    $incl = $this->helper('checkout')->getPriceInclTax($parentItem);

                    if ($typeOfDisplay014) {
                        $price = $incl + $weeeTaxAppliedAmount;
                    } else {
                        $price = $incl - $weeeTaxDisposition;
                    }

                    $config = array(
                        'value' => $this->_formatPrice($price)
                    );
                    if ($taxHelper->displaySalesBothPrices()) {
                        $config['label'] = $this->__('Incl. Tax');
                    }

                    $inclPriceXml = $priceXml->addCustomChild(
                        'including_tax',
                        null,
                        $config
                    );

                    if ($weeeTaxes) {
                        if ($typeOfDisplay1) {
                            $weeeXml = $inclPriceXml->addChild('weee');
                            foreach ($weeeTaxes as $tax) {
                                $weeeXml->addCustomChild(
                                    'tax',
                                    $this->_formatPrice($tax['amount']),
                                    array('label' => $tax['title'])
                                );
                            }
                        } elseif ($typeOfDisplay2 || $typeOfDisplay4) {
                            $weeeXml = $inclPriceXml->addChild('weee');
                            foreach ($weeeTaxes as $tax) {
                                $weeeXml->addCustomChild(
                                    'tax',
                                    $this->_formatPrice($tax['amount_incl_tax']),
                                    array('label' => $tax['title'])
                                );
                            }
                        }

                        if ($typeOfDisplay2) {
                            if (!isset($weeeXml)) {
                                $weeeXml = $inclPriceXml->addChild('weee');
                            }
                            $weeeXml->addCustomChild(
                                'total',
                                $this->_formatPrice(
                                    $incl + $weeeTaxAppliedAmount
                                ),
                                array('label' => $weeeHelper->__('Total incl. tax'))
                            );
                        }
                        if (isset($weeeXml)) {
                            unset($weeeXml);
                        }
                    }
                }
            }

            // set quantities
            /** @var $qtyXml Mage_XmlConnect_Model_Simplexml_Element */
            if (($isOption && $this->isChildCalculated())
                || (!$isOption && !$this->isChildCalculated())
            ) {
                $qtyXml = $objectXml->addChild('qty');
                if ($_item->getQtyOrdered() > 0) {
                    $qtyXml->addCustomChild(
                        'value',
                        $_item->getQtyOrdered() * 1,
                        array('label' => Mage::helper('sales')->__('Ordered'))
                    );
                }
                if ($_item->getQtyShipped() > 0 && !$this->isShipmentSeparately()) {
                    $qtyXml->addCustomChild(
                        'value',
                        $_item->getQtyShipped() * 1,
                        array('label' => Mage::helper('sales')->__('Shipped'))
                    );
                }
                if ($_item->getQtyCanceled() > 0) {
                    $qtyXml->addCustomChild(
                        'value',
                        $_item->getQtyCanceled() * 1,
                        array('label' => Mage::helper('sales')->__('Canceled'))
                    );
                }
                if ($_item->getQtyRefunded() > 0) {
                    $qtyXml->addCustomChild(
                        'value',
                        $_item->getQtyRefunded() * 1,
                        array('label' => Mage::helper('sales')->__('Refunded'))
                    );
                }
            } elseif ($_item->getQtyShipped() > 0 && $isOption && $this->isShipmentSeparately()) {
                $qtyXml = $objectXml->addChild('qty');
                $qtyXml->addCustomChild(
                    'value',
                    $_item->getQtyShipped() * 1,
                    array('label' => Mage::helper('sales')->__('Shipped'))
                );
            }

            // set subtotals exactly for the Bundle product, but not for related products
            if (!$isOption) {
                /** @var $subtotalXml Mage_XmlConnect_Model_Simplexml_Element */
                $subtotalXml = $objectXml->addChild('subtotal');

                // Subtotal excluding tax
                if ($taxHelper->displaySalesBothPrices() || $taxHelper->displaySalesPriceExclTax()) {
                    if ($typeOfDisplay014) {
                        $subtotal = $parentItem->getRowTotal()
                            + $parentItem->getWeeeTaxAppliedRowAmount()
                            + $parentItem->getWeeeTaxRowDisposition();
                    } else {
                        $subtotal = $parentItem->getRowTotal();
                    }

                    $config = array(
                        'value' => $this->_formatPrice($subtotal)
                    );
                    if ($taxHelper->displaySalesBothPrices()) {
                        $config['label'] = $this->__('Excl. Tax');
                    }
                    $exclPriceXml = $subtotalXml->addCustomChild(
                        'excluding_tax',
                        null,
                        $config
                    );

                    if ($weeeTaxes) {
                        /** @var $weeeXml Mage_XmlConnect_Model_Simplexml_Element */
                        if ($typeOfDisplay1) {
                            $weeeXml = $exclPriceXml->addChild('weee');
                            foreach ($weeeTaxes as $tax) {
                                $weeeXml->addCustomChild(
                                    'tax',
                                    $this->_formatPrice($tax['row_amount']),
                                    array('label' => $tax['title'])
                                );
                            }
                        } elseif ($typeOfDisplay2 || $typeOfDisplay4) {
                            $weeeXml = $exclPriceXml->addChild('weee');
                            foreach ($weeeTaxes as $tax) {
                                $weeeXml->addCustomChild(
                                    'tax',
                                    $this->_formatPrice($tax['row_amount_incl_tax']),
                                    array('label' => $tax['title'])
                                );
                            }
                        }

                        if ($typeOfDisplay2) {
                            if (!isset($weeeXml)) {
                                $weeeXml = $exclPriceXml->addChild('weee');
                            }
                            $weeeXml->addCustomChild(
                                'total',
                                $this->_formatPrice(
                                    $parentItem->getRowTotal()
                                    + $parentItem->getWeeeTaxAppliedRowAmount()
                                    + $parentItem->getWeeeTaxRowDisposition()
                                ),
                                array('label' => $weeeHelper->__('Total'))
                            );
                        }
                        if (isset($weeeXml)) {
                            unset($weeeXml);
                        }
                    }
                }

                // Subtotal including tax
                if ($taxHelper->displaySalesBothPrices() || $taxHelper->displaySalesPriceInclTax()) {
                    $incl = $this->helper('checkout')->getSubtotalInclTax($parentItem);

                    if ($typeOfDisplay014) {
                        $subtotal = $incl + $parentItem->getWeeeTaxAppliedRowAmount();
                    } else {
                        $subtotal = $incl - $parentItem->getWeeeTaxRowDisposition();
                    }

                    $config = array(
                        'value' => $this->_formatPrice($subtotal)
                    );
                    if ($taxHelper->displaySalesBothPrices()) {
                        $config['label'] = $this->__('Incl. Tax');
                    }

                    $inclPriceXml = $subtotalXml->addCustomChild(
                        'including_tax',
                        null,
                        $config
                    );

                    if ($weeeTaxes) {
                        if ($typeOfDisplay1) {
                            $weeeXml = $inclPriceXml->addChild('weee');
                            foreach ($weeeTaxes as $tax) {
                                $weeeXml->addCustomChild(
                                    'tax',
                                    $this->_formatPrice($tax['row_amount']),
                                    array('label' => $tax['title'])
                                );
                            }
                        } elseif ($typeOfDisplay2 || $typeOfDisplay4) {
                            $weeeXml = $inclPriceXml->addChild('weee');
                            foreach ($weeeTaxes as $tax) {
                                $weeeXml->addCustomChild(
                                    'tax',
                                    $this->_formatPrice($tax['row_amount_incl_tax']),
                                    array('label' => $tax['title'])
                                );
                            }
                        }

                        if ($typeOfDisplay2) {
                            if (!isset($weeeXml)) {
                                $weeeXml = $inclPriceXml->addChild('weee');
                            }
                            $weeeXml->addCustomChild(
                                'total',
                                $this->_formatPrice(
                                    $incl + $parentItem->getWeeeTaxAppliedRowAmount()
                                ),
                                array('label' => $weeeHelper->__('Total incl. tax'))
                            );
                        }
                        if (isset($weeeXml)) {
                            unset($weeeXml);
                        }
                    }
                }
            }
        }

        if ($parentItem->getDescription()) {
            $itemXml->addCustomChild(
                'description',
                $parentItem->getDescription()
            );
        }

        if ($options = $this->getItemOptions()) {
            /** @var $optionsXml Mage_XmlConnect_Model_Simplexml_Element */
            $optionsXml = $itemXml->addChild('options');
            foreach ($options as $option) {
                $formatedOptionValue = $this->getFormatedOptionValue($option);
                if (isset($formatedOptionValue['full_view']) && isset($formatedOptionValue['value'])) {
                    $value = $formatedOptionValue['value'];
                } elseif (isset($option['print_value'])) {
                    $value = $option['print_value'];
                } else {
                    $value = $option['value'];
                }

                if ($value) {
                    $optionsXml->addCustomChild(
                        'option',
                        strip_tags($value),
                        array('label' => $option['label'])
                    );
                }
            }
        }
    }

    /**
     * Prepare option data for output
     *
     * @param Mage_Sales_Model_Order_Item $item
     * @return string
     */
    public function getValueHtml($item)
    {
        $attributes = $this->getSelectionAttributes($item);
        if ($attributes) {
            return sprintf('%d', $attributes['qty']) . ' x '
                . $item->getName()
                . ' - ' . $this->_formatPrice($attributes['price']);
        } else {
            return $item->getName();
        }
    }

    /**
     * Format price using order currency
     *
     * @param float $price
     * @return string
     */
    protected function _formatPrice($price)
    {
        return $this->getOrder()->getOrderCurrency()->formatPrecision($price, 2, array(), false);
    }
}
