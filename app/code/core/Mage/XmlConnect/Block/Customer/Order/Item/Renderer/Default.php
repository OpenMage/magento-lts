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
 * Customer order details item xml
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Customer_Order_Item_Renderer_Default
    extends Mage_Sales_Block_Order_Item_Renderer_Default
{
    /**
     * Add item to XML object
     *
     * @param Mage_XmlConnect_Model_Simplexml_Element $orderItemXmlObj
     * @return void
     */
    public function addItemToXmlObject(Mage_XmlConnect_Model_Simplexml_Element $orderItemXmlObj)
    {
        /** @var $item Mage_Sales_Model_Order_Item */
        $item = $this->getItem();

        /** @var $itemXml Mage_XmlConnect_Model_Simplexml_Element */
        $itemXml = $orderItemXmlObj->addCustomChild(
            'item',
            null,
            array(
                'product_id'    => $item->getProductId()
            )
        );
        $itemXml->addCustomChild('name', $item->getName());

        /** @var $weeeHelper Mage_Weee_Helper_Data */
        $weeeHelper = $this->helper('weee');
        /** @var $taxHelper Mage_Tax_Helper_Data */
        $taxHelper  = $this->helper('tax');

        $options = $this->getItemOptions();
        if ($options) {
            /** @var $optionsXml Mage_XmlConnect_Model_Simplexml_Element */
            $optionsXml = $itemXml->addChild('options');
            foreach ($options as $option) {
                $value = false;
                $formatedOptionValue = $this->getFormatedOptionValue($option);
                if (isset($formatedOptionValue['full_view']) && isset($formatedOptionValue['value'])) {
                    $value = $formatedOptionValue['value'];
                } elseif (isset($option['print_value'])) {
                    $value = $option['print_value'];
                } elseif (isset($option['value'])) {
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

            $addtInfoBlock = $this->getProductAdditionalInformationBlock();
            if ($addtInfoBlock) {
                // TODO: find how to set additional info block
                // $addtInfoBlock->setItem($item)->toHtml();
            }
        }

        $itemXml->addCustomChild('entity_type', $item->getProductType());
        $itemXml->addCustomChild('description', $item->getDescription());
        $itemXml->addCustomChild('sku', Mage::helper('core/string')->splitInjection($this->getSku()));

        /** @var $priceXml Mage_XmlConnect_Model_Simplexml_Element */
        $priceXml = $itemXml->addChild('price');
        $weeeTaxAppliedAmount   = (float)$item->getWeeeTaxAppliedAmount();
        $weeeTaxDisposition     = (float)$item->getWeeeTaxDisposition();
        $typeOfDisplay1 = $weeeHelper->typeOfDisplay($item, 1, 'sales') && $weeeTaxAppliedAmount;
        $typeOfDisplay2 = $weeeHelper->typeOfDisplay($item, 2, 'sales') && $weeeTaxAppliedAmount;
        $typeOfDisplay4 = $weeeHelper->typeOfDisplay($item, 4, 'sales') && $weeeTaxAppliedAmount;
        $typeOfDisplay014 = $weeeHelper->typeOfDisplay($item, array(0, 1, 4), 'sales') && $weeeTaxAppliedAmount;
        $weeeTaxes = $weeeHelper->getApplied($item);

        // Price excluding tax
        if ($taxHelper->displaySalesBothPrices() || $taxHelper->displaySalesPriceExclTax()) {
            if ($typeOfDisplay014) {
                $price = $item->getPrice() + $weeeTaxAppliedAmount + $weeeTaxDisposition;
            } else {
                $price = $item->getPrice();
            }

            $config = array(
                'value' => $this->_formatPrice($price)
            );
            if ($taxHelper->displaySalesBothPrices()) {
                $config['label'] = $this->__('Excl. Tax');
            }

            /** @var $exclPriceXml Mage_XmlConnect_Model_Simplexml_Element */
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
                            $item->getPrice() + $weeeTaxAppliedAmount + $weeeTaxDisposition
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
            $incl = $this->helper('checkout')->getPriceInclTax($item);

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

            /** @var $inclPriceXml Mage_XmlConnect_Model_Simplexml_Element */
            $inclPriceXml = $priceXml->addCustomChild(
                'including_tax',
                null,
                $config
            );

            if ($weeeTaxes) {
                /** @var $weeeXml Mage_XmlConnect_Model_Simplexml_Element */
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
                        $this->_formatPrice($incl + $weeeTaxAppliedAmount),
                        array('label' => $weeeHelper->__('Total incl. tax'))
                    );
                }
                if (isset($weeeXml)) {
                    unset($weeeXml);
                }
            }
        }

        // Quantity: Ordered, Shipped, Cancelled, Refunded
        /** @var $quantityXml Mage_XmlConnect_Model_Simplexml_Element */
        $quantityXml = $itemXml->addChild('qty');
        $qty = 1 * $item->getQtyOrdered();
        if ($qty > 0) {
            $quantityXml->addCustomChild(
                'value',
                $qty,
                array('label' => $this->__('Ordered'))
            );
        }
        $qty = 1 * $item->getQtyShipped();
        if ($qty > 0) {
            $quantityXml->addCustomChild(
                'value',
                $qty,
                array('label' => $this->__('Shipped'))
            );
        }
        $qty = 1 * $item->getQtyCanceled();
        if ($qty > 0) {
            $quantityXml->addCustomChild(
                'value',
                $qty,
                array('label' => $this->__('Canceled'))
            );
        }
        $qty = 1 * $item->getQtyRefunded();
        if ($qty > 0) {
            $quantityXml->addCustomChild(
                'value',
                $qty,
                array('label' => $this->__('Refunded'))
            );
        }

        // Subtotal excluding tax
        /** @var $subtotalXml Mage_XmlConnect_Model_Simplexml_Element */
        $subtotalXml = $itemXml->addChild('subtotal');

        if ($taxHelper->displaySalesBothPrices() || $taxHelper->displaySalesPriceExclTax()) {

            if ($typeOfDisplay014) {
                $subtotal = $item->getRowTotal()
                    + $item->getWeeeTaxAppliedRowAmount()
                    + $item->getWeeeTaxRowDisposition();
            } else {
                $subtotal = $item->getRowTotal();
            }

            $config = array(
                'value' => $this->_formatPrice($subtotal)
            );
            if ($taxHelper->displaySalesBothPrices()) {
                $config['label'] = $this->__('Excl. Tax');
            }

            /** @var $exclPriceXml Mage_XmlConnect_Model_Simplexml_Element */
            $exclPriceXml = $subtotalXml->addCustomChild(
                'excluding_tax',
                null,
                $config
            );

            if ($weeeTaxes) {
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
                            $item->getRowTotal()
                            + $item->getWeeeTaxAppliedRowAmount()
                            + $item->getWeeeTaxRowDisposition()
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
            $incl = $this->helper('checkout')->getSubtotalInclTax($item);

            if ($typeOfDisplay014) {
                $subtotal = $incl + $item->getWeeeTaxAppliedRowAmount();
            } else {
                $subtotal = $incl - $item->getWeeeTaxRowDisposition();
            }

            $config = array(
                'value' => $this->_formatPrice($subtotal)
            );
            if ($taxHelper->displaySalesBothPrices()) {
                $config['label'] = $this->__('Incl. Tax');
            }

            /** @var $inclPriceXml Mage_XmlConnect_Model_Simplexml_Element */
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
                        $this->_formatPrice($incl + $item->getWeeeTaxAppliedRowAmount()),
                        array('label' => $weeeHelper->__('Total incl. tax'))
                    );
                }
            }
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
