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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @copyright  Copyright (c) 2006-2016 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Default product price xml renderer
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Catalog_Product_Price_Default extends Mage_Catalog_Block_Product_Price
{
    /**
     * Collect product prices to specified item xml object
     *
     * @param Mage_Catalog_Model_Product $product
     * @param Mage_XmlConnect_Model_Simplexml_Element $item
     */
    public function collectProductPrices(Mage_Catalog_Model_Product $product,
        Mage_XmlConnect_Model_Simplexml_Element $item)
    {
        $this->setProduct($product)->setDisplayMinimalPrice(true)->setUseLinkForAsLowAs(false);

        $priceXmlObj = $item->addChild('price');
        $tierPrices = $this->_getTierPrices($product);
        if (count($tierPrices) > 0) {
            $tierPricesTextArray = $item->escapeXml(implode(
                PHP_EOL, $this->_getTierPricesTextArray($tierPrices, $product)
            ));
            $item->addCustomChild('price_tier', $tierPricesTextArray);
        }

        /** @var $coreHelper Mage_Core_Helper_Data */
        $coreHelper = $this->helper('core');
        /** @var $weeeHelper Mage_Weee_Helper_Data */
        $weeeHelper = $this->helper('weee');
        /** @var $taxHelper Mage_Tax_Helper_Data */
        $taxHelper  = $this->helper('tax');

        $simplePricesTax = ($taxHelper->displayPriceIncludingTax() || $taxHelper->displayBothPrices());
        $minimalPriceValue = $product->getMinimalPrice();
        $minimalPrice = $taxHelper->getPrice($product, $minimalPriceValue, $simplePricesTax);

        if (!$product->isGrouped()) {
            $weeeTaxAmount = $weeeHelper->getAmountForDisplay($product);
            if ($weeeHelper->typeOfDisplay($product, array(1, 2, 4))) {
                $weeeTaxAmount = $weeeHelper->getAmount($product);
                $weeeTaxAttributes = $weeeHelper->getProductWeeeAttributesForDisplay($product);
            }

            $price = $taxHelper->getPrice($product, $product->getPrice());
            $regularPrice = $taxHelper->getPrice($product, $product->getPrice(), $simplePricesTax);
            $finalPrice = $taxHelper->getPrice($product, $product->getFinalPrice());
            $finalPriceInclTax = $taxHelper->getPrice($product, $product->getFinalPrice(), true);
            $weeeHelper->getPriceDisplayType();
            if ($finalPrice == $price) {
                if ($taxHelper->displayBothPrices()) {
                    /**
                     * Including
                     */
                    if ($weeeTaxAmount && $weeeHelper->typeOfDisplay($product, 0)) {
                        $priceXmlObj->addAttribute(
                            'excluding_tax', $coreHelper->currency($price + $weeeTaxAmount, true, false)
                        );
                        $priceXmlObj->addAttribute(
                            'including_tax', $coreHelper->currency($finalPriceInclTax + $weeeTaxAmount, true, false)
                        );
                    } elseif ($weeeTaxAmount && $weeeHelper->typeOfDisplay($product, 1)) {
                        /**
                         * Including + Weee
                         */
                        $priceXmlObj->addAttribute(
                            'excluding_tax', $coreHelper->currency($price + $weeeTaxAmount, true, false)
                        );
                        $priceXmlObj->addAttribute(
                            'including_tax', $coreHelper->currency($finalPriceInclTax + $weeeTaxAmount, true, false)
                        );
                        $weeeXmlObj = $priceXmlObj->addChild('weee');
                        $weeeSeparator = ' + ';
                        $weeeXmlObj->addAttribute('separator', $weeeSeparator);
                        foreach ($weeeTaxAttributes as $weeeTaxAttribute) {
                            $weeeItemXmlObj = $weeeXmlObj->addChild('item');
                            $weeeItemXmlObj->addAttribute(
                                'name', $weeeItemXmlObj->escapeXml($weeeTaxAttribute->getName())
                            );
                            $weeeItemXmlObj->addAttribute(
                                'amount', $coreHelper->currency($weeeTaxAttribute->getAmount(), true, false)
                            );
                        }
                    } elseif ($weeeTaxAmount && $weeeHelper->typeOfDisplay($product, 4)) {
                        /**
                         * Including + Weee
                         */
                        $priceXmlObj->addAttribute(
                            'excluding_tax', $coreHelper->currency($price + $weeeTaxAmount, true, false)
                        );
                        $priceXmlObj->addAttribute(
                            'including_tax', $coreHelper->currency($finalPriceInclTax + $weeeTaxAmount, true, false)
                        );
                        $weeeXmlObj = $priceXmlObj->addChild('weee');
                        $weeeSeparator = ' + ';
                        $weeeXmlObj->addAttribute('separator', $weeeSeparator);
                        foreach ($weeeTaxAttributes as $weeeTaxAttribute) {
                            $weeeItemXmlObj = $weeeXmlObj->addChild('item');
                            $weeeItemXmlObj->addAttribute(
                                'name', $weeeItemXmlObj->escapeXml($weeeTaxAttribute->getName())
                            );
                            $weeeItemXmlObj->addAttribute('amount', $coreHelper->currency(
                                $weeeTaxAttribute->getAmount() + $weeeTaxAttribute->getTaxAmount(), true, false
                            ));
                        }
                    } elseif ($weeeTaxAmount && $weeeHelper->typeOfDisplay($product, 2)) {
                        /**
                         * Excluding + Weee + Final
                         */
                        $priceXmlObj->addAttribute('excluding_tax', $coreHelper->currency($price, true, false));
                        $weeeXmlObj = $priceXmlObj->addChild('weee');
                        foreach ($weeeTaxAttributes as $weeeTaxAttribute) {
                            $weeeItemXmlObj = $weeeXmlObj->addChild('item');
                            $weeeItemXmlObj->addAttribute(
                                'name', $weeeItemXmlObj->escapeXml($weeeTaxAttribute->getName())
                            );
                            $weeeItemXmlObj->addAttribute(
                                'amount', $coreHelper->currency($weeeTaxAttribute->getAmount(), true, false)
                            );
                        }
                        $priceXmlObj->addAttribute('including_tax', $coreHelper->currency(
                            $finalPriceInclTax + $weeeTaxAmount, true, false
                        ));
                    } else {
                        $priceXmlObj->addAttribute('excluding_tax', $coreHelper->currency($price, true, false));
                        $priceXmlObj->addAttribute(
                            'including_tax', $coreHelper->currency($finalPriceInclTax, true, false)
                        );
                    }
                /**
                 * if ($taxHelper->displayBothPrices()) {
                 */
                } else {
                    /**
                     * Including
                     */
                    if ($weeeTaxAmount && $weeeHelper->typeOfDisplay($product, 0)) {
                        $priceXmlObj->addAttribute('regular', $coreHelper->currency(
                            $price + $weeeTaxAmount, true, false
                        ));
                    } elseif ($weeeTaxAmount && $weeeHelper->typeOfDisplay($product, 1)) {
                        /**
                         * Including + Weee
                         */
                        $priceXmlObj->addAttribute('regular', $coreHelper->currency(
                            $price + $weeeTaxAmount, true, false
                        ));
                        $weeeXmlObj = $priceXmlObj->addChild('weee');
                        $weeeSeparator = ' + ';
                        $weeeXmlObj->addAttribute('separator', $weeeSeparator);
                        foreach ($weeeTaxAttributes as $weeeTaxAttribute) {
                            $weeeItemXmlObj = $weeeXmlObj->addChild('item');
                            $weeeItemXmlObj->addAttribute(
                                'name', $weeeItemXmlObj->escapeXml($weeeTaxAttribute->getName())
                            );
                            $weeeItemXmlObj->addAttribute(
                                'amount', $coreHelper->currency($weeeTaxAttribute->getAmount(), true, false)
                            );
                        }
                    } elseif ($weeeTaxAmount && $weeeHelper->typeOfDisplay($product, 4)) {
                    /**
                     * Including + Weee
                     */
                        $priceXmlObj->addAttribute('regular', $coreHelper->currency(
                            $price + $weeeTaxAmount, true, false
                        ));
                        $weeeXmlObj = $priceXmlObj->addChild('weee');
                        $weeeSeparator = ' + ';
                        $weeeXmlObj->addAttribute('separator', $weeeSeparator);
                        foreach ($weeeTaxAttributes as $weeeTaxAttribute) {
                            $weeeItemXmlObj = $weeeXmlObj->addChild('item');
                            $weeeItemXmlObj->addAttribute(
                                'name', $weeeItemXmlObj->escapeXml($weeeTaxAttribute->getName())
                            );
                            $weeeItemXmlObj->addAttribute('amount', $coreHelper->currency(
                                $weeeTaxAttribute->getAmount() + $weeeTaxAttribute->getTaxAmount(), true, false
                            ));
                        }
                    } elseif ($weeeTaxAmount && $weeeHelper->typeOfDisplay($product, 2)) {
                        /**
                         * Excluding + Weee + Final
                         */
                        $priceXmlObj->addAttribute('regular', $coreHelper->currency($price, true, false));
                        $weeeXmlObj = $priceXmlObj->addChild('weee');
                        foreach ($weeeTaxAttributes as $weeeTaxAttribute) {
                            $weeeItemXmlObj = $weeeXmlObj->addChild('item');
                            $weeeItemXmlObj->addAttribute(
                                'name', $weeeItemXmlObj->escapeXml($weeeTaxAttribute->getName())
                            );
                            $weeeItemXmlObj->addAttribute(
                                'amount', $coreHelper->currency($weeeTaxAttribute->getAmount(), true, false)
                            );
                        }
                        $priceXmlObj->addAttribute(
                            'including_tax', $coreHelper->currency($price + $weeeTaxAmount, true, false)
                        );
                    } else {
                         $priceXmlObj->addAttribute('regular', $coreHelper->currency($price, true, false));
                    }
                }
            /**
             * if ($finalPrice == $price) {
             */
            } else {
                $originalWeeeTaxAmount = $weeeHelper->getOriginalAmount($product);
                /**
                 * Including
                 */
                if ($weeeTaxAmount && $weeeHelper->typeOfDisplay($product, 0)) {
                    $priceXmlObj->addAttribute('regular', $coreHelper->currency(
                        $regularPrice + $originalWeeeTaxAmount, true, false
                    ));
                    if ($taxHelper->displayBothPrices()) {
                        $priceXmlObj->addAttribute('special_excluding_tax', $coreHelper->currency(
                            $finalPrice + $weeeTaxAmount, true, false
                        ));
                        $priceXmlObj->addAttribute('special_including_tax', $coreHelper->currency(
                            $finalPriceInclTax + $weeeTaxAmount, true, false
                        ));
                    } else {
                        $priceXmlObj->addAttribute('special', $coreHelper->currency(
                            $finalPrice + $weeeTaxAmount, true, false
                        ));
                    }
                } elseif ($weeeTaxAmount && $weeeHelper->typeOfDisplay($product, 1)) {
                    /**
                     * Including + Weee
                     */
                    $priceXmlObj->addAttribute('regular', $coreHelper->currency(
                        $regularPrice + $originalWeeeTaxAmount, true, false
                    ));
                    $priceXmlObj->addAttribute('special_excluding_tax', $coreHelper->currency(
                        $finalPrice + $weeeTaxAmount, true, false
                    ));
                    $weeeXmlObj = $priceXmlObj->addChild('weee');
                    $weeeSeparator = ' + ';
                    $weeeXmlObj->addAttribute('separator', $weeeSeparator);
                    foreach ($weeeTaxAttributes as $weeeTaxAttribute) {
                        $weeeItemXmlObj = $weeeXmlObj->addChild('item');
                        $weeeItemXmlObj->addAttribute(
                            'name', $weeeItemXmlObj->escapeXml($weeeTaxAttribute->getName())
                        );
                        $weeeItemXmlObj->addAttribute('amount', $coreHelper->currency(
                            $weeeTaxAttribute->getAmount(), true, false
                        ));
                    }
                    $priceXmlObj->addAttribute('special_including_tax', $coreHelper->currency(
                        $finalPriceInclTax + $weeeTaxAmount, true, false
                    ));
                } elseif ($weeeTaxAmount && $weeeHelper->typeOfDisplay($product, 4)) {
                    /**
                     * Including + Weee
                     */
                    $priceXmlObj->addAttribute('regular', $coreHelper->currency(
                        $regularPrice + $originalWeeeTaxAmount, true, false
                    ));
                    $priceXmlObj->addAttribute('special_excluding_tax', $coreHelper->currency(
                        $finalPrice + $weeeTaxAmount, true, false
                    ));
                    $weeeXmlObj = $priceXmlObj->addChild('weee');
                    $weeeSeparator = ' + ';
                    $weeeXmlObj->addAttribute('separator', $weeeSeparator);
                    foreach ($weeeTaxAttributes as $weeeTaxAttribute) {
                        $weeeItemXmlObj = $weeeXmlObj->addChild('item');
                        $weeeItemXmlObj->addAttribute(
                            'name', $weeeItemXmlObj->escapeXml($weeeTaxAttribute->getName())
                        );
                        $weeeItemXmlObj->addAttribute('amount', $coreHelper->currency(
                            $weeeTaxAttribute->getAmount() + $weeeTaxAttribute->getTaxAmount(), true, false
                        ));
                    }
                    $priceXmlObj->addAttribute('special_including_tax', $coreHelper->currency(
                        $finalPriceInclTax + $weeeTaxAmount, true, false
                    ));
                } elseif ($weeeTaxAmount && $weeeHelper->typeOfDisplay($product, 2)) {
                    /**
                     * Excluding + Weee + Final
                     */
                    $priceXmlObj->addAttribute('regular', $coreHelper->currency($regularPrice, true, false));
                    $priceXmlObj->addAttribute(
                        'special_excluding_tax', $coreHelper->currency($finalPrice, true, false)
                    );
                    $weeeXmlObj = $priceXmlObj->addChild('weee');
                    foreach ($weeeTaxAttributes as $weeeTaxAttribute) {
                        $weeeItemXmlObj = $weeeXmlObj->addChild('item');
                        $weeeItemXmlObj->addAttribute(
                            'name', $weeeItemXmlObj->escapeXml($weeeTaxAttribute->getName())
                        );
                        $weeeItemXmlObj->addAttribute('amount', $coreHelper->currency(
                            $weeeTaxAttribute->getAmount(), true, false
                        ));
                    }
                    $priceXmlObj->addAttribute('special_including_tax', $coreHelper->currency(
                        $finalPriceInclTax + $weeeTaxAmount, true, false
                    ));
                } else {
                    /**
                     * Excluding
                     */
                    $priceXmlObj->addAttribute('regular', $coreHelper->currency($regularPrice, true, false));
                    if ($taxHelper->displayBothPrices()) {
                        $priceXmlObj->addAttribute(
                            'special_excluding_tax', $coreHelper->currency($finalPrice, true, false)
                        );
                        $priceXmlObj->addAttribute(
                            'special_including_tax', $coreHelper->currency($finalPriceInclTax, true, false)
                        );
                    } else {
                        $priceXmlObj->addAttribute(
                            'special', $coreHelper->currency($finalPrice, true, false)
                        );
                    }
                }
            }

            if ($this->getDisplayMinimalPrice() && $minimalPriceValue
                && $minimalPriceValue < $product->getFinalPrice()
            ) {
                $minimalPriceDisplayValue = $minimalPrice;

                if ($weeeTaxAmount && $weeeHelper->typeOfDisplay($product, array(0, 1, 4))) {
                    $minimalPriceDisplayValue = $minimalPrice + $weeeTaxAmount;
                }

                if (!$this->getUseLinkForAsLowAs()) {
                    $priceXmlObj->addAttribute('as_low_as', $coreHelper->currency(
                        $minimalPriceDisplayValue, true, false
                    ));
                }
            }
        /**
         * if (!$product->isGrouped()) {
         */
        } else {
            $exclTax = $taxHelper->getPrice($product, $minimalPriceValue, null);
            $inclTax = $taxHelper->getPrice($product, $minimalPriceValue, true);

            if ($this->getDisplayMinimalPrice() && $minimalPriceValue) {
                if ($taxHelper->displayBothPrices()) {
                    $priceXmlObj->addAttribute('starting_at_excluding_tax', $coreHelper->currency(
                        $exclTax, true, false
                    ));
                    $priceXmlObj->addAttribute('starting_at_including_tax', $coreHelper->currency(
                        $inclTax, true, false
                    ));
                } else {
                    $showPrice = $inclTax;
                    if (!$taxHelper->displayPriceIncludingTax()) {
                        $showPrice = $exclTax;
                    }
                    $priceXmlObj->addAttribute('starting_at', $coreHelper->currency($showPrice, true, false));
                }
            }
        }
    }

    /**
     * Get tier prices (formatted)
     *
     * @param Mage_Catalog_Model_Product $product
     * @return array
     */
    protected function _getTierPrices(Mage_Catalog_Model_Product $product)
    {
        if (is_null($product)) {
            return array();
        }
        $prices  = $product->getFormatedTierPrice();

        $res = array();
        if (is_array($prices)) {
            foreach ($prices as $price) {
                $price['price_qty'] = $price['price_qty']*1;
                if ($product->getPrice() != $product->getFinalPrice()) {
                    if ($price['price'] < $product->getFinalPrice()) {
                        $price['savePercent'] = ceil(100 - ((100 / $product->getFinalPrice()) * $price['price']));
                        $price['formated_price'] = Mage::app()->getStore()->formatPrice(
                            Mage::app()->getStore()->convertPrice(
                                Mage::helper('tax')->getPrice($product, $price['website_price'])
                            ),
                            false
                        );
                        $price['formated_price_incl_tax'] = Mage::app()->getStore()->formatPrice(
                            Mage::app()->getStore()->convertPrice(Mage::helper('tax')->getPrice(
                                $product, $price['website_price'], true
                            )),
                            false
                        );
                        $res[] = $price;
                    }
                } else {
                    if ($price['price'] < $product->getPrice()) {
                        $price['savePercent'] = ceil(100 - ((100 / $product->getPrice()) * $price['price']));
                        $price['formated_price'] = Mage::app()->getStore()->formatPrice(
                            Mage::app()->getStore()->convertPrice(Mage::helper('tax')->getPrice(
                                $product, $price['website_price']
                            )),
                            false
                        );
                        $price['formated_price_incl_tax'] = Mage::app()->getStore()->formatPrice(
                            Mage::app()->getStore()->convertPrice(Mage::helper('tax')->getPrice(
                                $product, $price['website_price'], true
                            )),
                            false
                        );
                        $res[] = $price;
                    }
                }
            }
        }
        return $res;
    }

    /**
     * Get tier prices (formatted) as array of strings
     *
     * @param array $tierPrices
     * @param Mage_Catalog_Model_Product $product
     * @return array
     */
    protected function _getTierPricesTextArray($tierPrices, $product)
    {
        $pricesArray = array();
        if (Mage::helper('weee')->typeOfDisplay($product, array(1, 2, 4))) {
            $weeeTaxAttributes = Mage::helper('weee')->getProductWeeeAttributesForDisplay($product);
        }

        if ($product->isGrouped()) {
            $tierPrices = $this->getTierPrices($product);
        }
        Mage::helper('weee')->processTierPrices($product, $tierPrices);

        foreach ($tierPrices as $price) {
            $s = '';
            if ($this->helper('tax')->displayBothPrices()) {
                if (Mage::helper('weee')->typeOfDisplay($product, 0)) {
                    $s .= $this->__('Buy %1$s for %2$s (%3$s incl. tax) each', $price['price_qty'], $price['formated_price_incl_weee_only'], $price['formated_price_incl_weee']);
                } else if (Mage::helper('weee')->typeOfDisplay($product, 1)) {
                    $s .= $this->__('Buy %1$s for %2$s', $price['price_qty'], $price['formated_price_incl_weee_only']);
                    if ($weeeTaxAttributes) {
                        $s .= ' (' . $this->__('%1$s incl tax.', $price['formated_price_incl_weee']);
                        $separator = ' + ';
                        foreach ($weeeTaxAttributes as $attribute) {
                            $s .= $separator . $attribute->getName() . ': ';
                            $s .= Mage::helper('core')->currency($attribute->getAmount());
                        }
                        $s .= ')';
                    }
                    $s .= ' ' . $this->__('each');
                } else if (Mage::helper('weee')->typeOfDisplay($product, 4)) {
                    $s .= $this->__('Buy %1$s for %2$s', $price['price_qty'], $price['formated_price_incl_weee_only']);
                    if ($weeeTaxAttributes) {
                        $s .= ' (' . $this->__('%1$s incl tax.', $price['formated_price_incl_weee']);
                        $separator = ' + ';
                        foreach ($weeeTaxAttributes as $attribute) {
                            $s .= $separator . $attribute->getName() . ': ';
                            $s .= Mage::helper('core')->currency(
                                $attribute->getAmount() + $attribute->getTaxAmount()
                            );
                        }
                        $s .= ')';
                    }
                    $s .= ' ' . $this->__('each');
                } else if (Mage::helper('weee')->typeOfDisplay($product, 2)) {
                    $s .= $this->__('Buy %1$s for %2$s', $price['price_qty'], $price['formated_price']);
                    if ($weeeTaxAttributes) {
                        $s .= ' (';
                        foreach ($weeeTaxAttributes as $attribute) {
                            $s .= $attribute->getName() . ': ';
                            $s .= Mage::helper('core')->currency($attribute->getAmount());
                        }
                        $s .= ' ' . $this->__('Total incl. Tax: %1$s', $price['formated_price_incl_weee']) . ')';
                    }
                    $s .= ' ' . $this->__('each');
                } else {
                    $s .= $this->__('Buy %1$s for %2$s (%3$s incl. tax) each', $price['price_qty'], $price['formated_price'], $price['formated_price_incl_tax']);
                }
            } else {
                if ($this->helper('tax')->displayPriceIncludingTax()) {
                    if (Mage::helper('weee')->typeOfDisplay($product, 0)) {
                        $s .= $this->__('Buy %1$s for %2$s each', $price['price_qty'], $price['formated_price_incl_weee']);
                    } else if (Mage::helper('weee')->typeOfDisplay($product, 1)) {
                        $s .= $this->__('Buy %1$s for %2$s', $price['price_qty'], $price['formated_price_incl_weee']);
                        if ($weeeTaxAttributes) {
                            $s .= ' (';
                            $separator = '';
                            foreach ($weeeTaxAttributes as $attribute) {
                                $s .= $separator . $attribute->getName() . ': ';
                                $s .= Mage::helper('core')->currency($attribute->getAmount());
                                $separator = ' + ';
                            }
                            $s .= ')';
                        }
                        $s .= ' ' . $this->__('each');
                    } else if (Mage::helper('weee')->typeOfDisplay($product, 4)) {
                        $s .= $this->__('Buy %1$s for %2$s', $price['price_qty'], $price['formated_price_incl_weee']);
                        if ($weeeTaxAttributes) {
                            $s .= ' (';
                            $separator = '';
                            foreach ($weeeTaxAttributes as $attribute) {
                                $s .= $separator . $attribute->getName() . ': ';
                                $s .= Mage::helper('core')->currency(
                                    $attribute->getAmount() + $attribute->getTaxAmount()
                                );
                                $separator = ' + ';
                            }
                            $s .= ')';
                        }
                        $s .= ' ' . $this->__('each');
                    } else if (Mage::helper('weee')->typeOfDisplay($product, 2)) {
                        $s .= $this->__('Buy %1$s for %2$s', $price['price_qty'], $price['formated_price_incl_tax']);
                        if ($weeeTaxAttributes) {
                            $s .= ' (';
                            foreach ($weeeTaxAttributes as $attribute) {
                                $s .= $attribute->getName() . ': ';
                                $s .= Mage::helper('core')->currency($attribute->getAmount());
                            }
                            $s .=  ' ' . $this->__('Total incl. Tax: %1$s', $price['formated_price_incl_weee']) . ')';
                        }
                        $s .= ' ' . $this->__('each');
                    } else {
                        $s .= $this->__('Buy %1$s for %2$s each', $price['price_qty'], $price['formated_price_incl_tax']);
                    }
                } else {
                    if (Mage::helper('weee')->typeOfDisplay($product, 0)) {
                        $s .= $this->__('Buy %1$s for %2$s each', $price['price_qty'], $price['formated_price_incl_weee_only']);
                    } else if (Mage::helper('weee')->typeOfDisplay($product, 1)) {
                        $s .= $this->__('Buy %1$s for %2$s', $price['price_qty'], $price['formated_price_incl_weee_only']);
                        if ($weeeTaxAttributes) {
                            $s .= ' (';
                            $separator = '';
                            foreach ($weeeTaxAttributes as $attribute) {
                                $s .= $separator . $attribute->getName() . ': ';
                                $s .= Mage::helper('core')->currency($attribute->getAmount());
                                $separator = ' + ';
                            }
                            $s .= ')';
                        }
                        $s .= ' ' . $this->__('each');
                    } else if (Mage::helper('weee')->typeOfDisplay($product, 4)) {
                        $s .= $this->__('Buy %1$s for %2$s', $price['price_qty'], $price['formated_price_incl_weee_only']);
                        if ($weeeTaxAttributes) {
                            $s .= ' (';
                            $separator = '';
                            foreach ($weeeTaxAttributes as $attribute) {
                                $s .= $separator . $attribute->getName() . ': ';
                                $s .= Mage::helper('core')->currency(
                                    $attribute->getAmount() + $attribute->getTaxAmount()
                                );
                                $separator = ' + ';
                            }
                            $s .= ')';
                        }
                        $s .= ' ' . $this->__('each');
                    } else if (Mage::helper('weee')->typeOfDisplay($product, 2)) {
                        $s .= $this->__('Buy %1$s for %2$s', $price['price_qty'], $price['formated_price']);
                        if ($weeeTaxAttributes) {
                            $s .= ' (';
                            foreach ($weeeTaxAttributes as $attribute) {
                                $s .= $attribute->getName() . ': ';
                                $s .= Mage::helper('core')->currency($attribute->getAmount());
                            }
                            $s .= ' ' . $this->__('Total incl. Tax: %1$s', $price['formated_price_incl_weee_only']) . ')';
                        }
                        $s .= ' ' . $this->__('each');
                    } else {
                        $s .= $this->__('Buy %1$s for %2$s each', $price['price_qty'], $price['formated_price']);
                    }
                }
            }
            if (!$product->isGrouped()) {
                $condition1 = ($product->getPrice() == $product->getFinalPrice()
                    && $product->getPrice() > $price['price']);

                $condition2 = ($product->getPrice() != $product->getFinalPrice()
                    && $product->getFinalPrice() > $price['price']);

                if ($condition1 || $condition2) {
                    $s .= ' ' . $this->__('and') . ' ' . $this->__('save') . ' ' . $price['savePercent'] . '%';
                }
            }
            $pricesArray[] = $s;
        }
        return $pricesArray;
    }
}
