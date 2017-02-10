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
 * @copyright  Copyright (c) 2006-2017 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Bundle product price xml renderer
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Catalog_Product_Price_Bundle extends Mage_Bundle_Block_Catalog_Product_Price
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

        /** @var $coreHelper Mage_Core_Helper_Data */
        $coreHelper = $this->helper('core');
        /** @var $weeeHelper Mage_Weee_Helper_Data */
        $weeeHelper = $this->helper('weee');
        /** @var $taxHelper Mage_Tax_Helper_Data */
        $taxHelper  = $this->helper('tax');

        $tierPrices = $this->_getTierPrices($product);

        if (count($tierPrices) > 0) {
            $tierPricesTextArray = array();
            foreach ($tierPrices as $price) {
                $discount = ' ' . ($price['price'] * 1) . '%';
                $tierPricesTextArray[] = $this->__('Buy %1$s with %2$s discount each', $price['price_qty'], $discount);
            }
            $item->addChild('price_tier', implode(PHP_EOL, $tierPricesTextArray));
        }

        list($minimalPrice, $maximalPrice) = $product->getPriceModel()->getPrices($product);

        $weeeTaxAmount = 0;
        $minimalPriceTax = $taxHelper->getPrice($product, $minimalPrice);
        $minimalPriceInclTax = $taxHelper->getPrice($product, $minimalPrice, true);

        if ($product->getPriceType() == 1) {
            $weeeTaxAmount = $weeeHelper->getAmount($product);
            if ($weeeTaxAmount && $weeeHelper->typeOfDisplay($product, array(0, 1, 4))) {
                $minimalPriceTax += $weeeTaxAmount;
                $minimalPriceInclTax += $weeeTaxAmount;
            }
            if ($weeeTaxAmount && $weeeHelper->typeOfDisplay($product, 2)) {
                $minimalPriceInclTax += $weeeTaxAmount;
            }

            if ($weeeHelper->typeOfDisplay($product, array(1, 2, 4))) {
                $weeeTaxAttributes = $weeeHelper->getProductWeeeAttributesForDisplay($product);
            }
        }

        if ($product->getPriceView()) {
            if ($taxHelper->displayBothPrices()) {
                $priceXmlObj->addAttribute(
                    'as_low_as_excluding_tax', $coreHelper->currency($minimalPriceTax, true, false)
                );
                if ($weeeTaxAmount && $product->getPriceType() == 1
                    && $weeeHelper->typeOfDisplay($product, array(2, 1, 4))
                ) {
                    $weeeXmlObj = $priceXmlObj->addChild('weee');
                    $weeeSeparator = ' + ';
                    $weeeXmlObj->addAttribute('separator', $weeeSeparator);
                    foreach ($weeeTaxAttributes as $weeeTaxAttribute) {
                        if ($weeeHelper->typeOfDisplay($product, array(2, 4))) {
                            $amount = $weeeTaxAttribute->getAmount() + $weeeTaxAttribute->getTaxAmount();
                        } else {
                            $amount = $weeeTaxAttribute->getAmount();
                        }
                        $weeeItemXmlObj = $weeeXmlObj->addChild('item');
                        $weeeItemXmlObj->addAttribute(
                            'name', $weeeItemXmlObj->escapeXml($weeeTaxAttribute->getName())
                        );
                        $weeeItemXmlObj->addAttribute('amount', $coreHelper->currency($amount, true, false));
                    }
                }
                $priceXmlObj->addAttribute(
                    'as_low_as_including_tax', $coreHelper->currency($minimalPriceInclTax, true, false)
                );
            } else {
                $priceXmlObj->addAttribute(
                    'as_low_as', $coreHelper->currency($minimalPriceTax, true, false)
                );
                if ($weeeTaxAmount && $product->getPriceType() == 1
                    && $weeeHelper->typeOfDisplay($product, array(2, 1, 4))
                ) {
                    $weeeXmlObj = $priceXmlObj->addChild('weee');
                    $weeeSeparator = ' + ';
                    $weeeXmlObj->addAttribute('separator', $weeeSeparator);
                    foreach ($weeeTaxAttributes as $weeeTaxAttribute) {
                        if ($weeeHelper->typeOfDisplay($product, array(2, 4))) {
                            $amount = $weeeTaxAttribute->getAmount() + $weeeTaxAttribute->getTaxAmount();
                        } else {
                            $amount = $weeeTaxAttribute->getAmount();
                        }
                        $weeeItemXmlObj = $weeeXmlObj->addChild('item');
                        $weeeItemXmlObj->addAttribute(
                            'name', $weeeItemXmlObj->escapeXml($weeeTaxAttribute->getName())
                        );
                        $weeeItemXmlObj->addAttribute(
                            'amount', $coreHelper->currency($amount, true, false)
                        );
                    }
                }
                if ($weeeHelper->typeOfDisplay($product, 2) && $weeeTaxAmount) {
                    $priceXmlObj->addAttribute(
                        'as_low_as_including_tax', $coreHelper->currency($minimalPriceInclTax, true, false)
                    );
                }
            }
        /**
         * if ($product->getPriceView()) {
         */
        } else {
            if ($minimalPrice <> $maximalPrice) {
                if ($taxHelper->displayBothPrices()) {
                    $priceXmlObj->addAttribute(
                        'from_excluding_tax', $coreHelper->currency($minimalPriceTax, true, false)
                    );
                    if ($weeeTaxAmount && $product->getPriceType() == 1
                        && $weeeHelper->typeOfDisplay($product, array(2, 1, 4))
                    ) {
                        $weeeXmlObj = $priceXmlObj->addChild('from_weee');
                        $weeeSeparator = ' + ';
                        $weeeXmlObj->addAttribute('separator', $weeeSeparator);
                        foreach ($weeeTaxAttributes as $weeeTaxAttribute) {
                            if ($weeeHelper->typeOfDisplay($product, array(2, 4))) {
                                $amount = $weeeTaxAttribute->getAmount() + $weeeTaxAttribute->getTaxAmount();
                            } else {
                                $amount = $weeeTaxAttribute->getAmount();
                            }
                            $weeeItemXmlObj = $weeeXmlObj->addChild('item');
                            $weeeItemXmlObj->addAttribute(
                                'name', $weeeItemXmlObj->escapeXml($weeeTaxAttribute->getName())
                            );
                            $weeeItemXmlObj->addAttribute('amount', $coreHelper->currency($amount, true, false));
                        }
                    }
                    $priceXmlObj->addAttribute(
                        'from_including_tax', $coreHelper->currency($minimalPriceInclTax, true, false)
                    );
                } else {
                    $priceXmlObj->addAttribute('from', $coreHelper->currency($minimalPriceTax, true, false));
                    if ($weeeTaxAmount && $product->getPriceType() == 1
                        && $weeeHelper->typeOfDisplay($product, array(2, 1, 4))
                    ) {
                        $weeeXmlObj = $priceXmlObj->addChild('from_weee');
                        $weeeSeparator = ' + ';
                        $weeeXmlObj->addAttribute('separator', $weeeSeparator);
                        foreach ($weeeTaxAttributes as $weeeTaxAttribute) {
                            if ($weeeHelper->typeOfDisplay($product, array(2, 4))) {
                                $amount = $weeeTaxAttribute->getAmount() + $weeeTaxAttribute->getTaxAmount();
                            } else {
                                $amount = $weeeTaxAttribute->getAmount();
                            }
                            $weeeItemXmlObj = $weeeXmlObj->addChild('item');
                            $weeeItemXmlObj->addAttribute(
                                'name', $weeeItemXmlObj->escapeXml($weeeTaxAttribute->getName())
                            );
                            $weeeItemXmlObj->addAttribute('amount', $coreHelper->currency($amount, true, false));
                        }
                    }
                    if ($weeeHelper->typeOfDisplay($product, 2) && $weeeTaxAmount) {
                        $priceXmlObj->addAttribute(
                            'from_including_tax', $coreHelper->currency($minimalPriceInclTax, true, false)
                        );
                    }
                }

                $maximalPriceTax = Mage::helper('tax')->getPrice($product, $maximalPrice);
                $maximalPriceInclTax = Mage::helper('tax')->getPrice($product, $maximalPrice, true);

                if ($product->getPriceType() == 1) {
                    if ($weeeTaxAmount && $weeeHelper->typeOfDisplay($product, array(0, 1, 4))) {
                        $maximalPriceTax += $weeeTaxAmount;
                        $maximalPriceInclTax += $weeeTaxAmount;
                    }
                    if ($weeeTaxAmount && $weeeHelper->typeOfDisplay($product, 2)) {
                        $maximalPriceInclTax += $weeeTaxAmount;
                    }
                }

                if ($taxHelper->displayBothPrices()) {
                    $priceXmlObj->addAttribute(
                        'to_excluding_tax', $coreHelper->currency($maximalPriceTax, true, false)
                    );
                    if ($weeeTaxAmount && $product->getPriceType() == 1
                        && $weeeHelper->typeOfDisplay($product, array(2, 1, 4))
                    ) {
                        $weeeXmlObj = $priceXmlObj->addChild('to_weee');
                        $weeeSeparator = ' + ';
                        $weeeXmlObj->addAttribute('separator', $weeeSeparator);
                        foreach ($weeeTaxAttributes as $weeeTaxAttribute) {
                            if ($weeeHelper->typeOfDisplay($product, array(2, 4))) {
                                $amount = $weeeTaxAttribute->getAmount() + $weeeTaxAttribute->getTaxAmount();
                            } else {
                                $amount = $weeeTaxAttribute->getAmount();
                            }
                            $weeeItemXmlObj = $weeeXmlObj->addChild('item');
                            $weeeItemXmlObj->addAttribute(
                                'name', $weeeItemXmlObj->escapeXml($weeeTaxAttribute->getName())
                            );
                            $weeeItemXmlObj->addAttribute('amount', $coreHelper->currency($amount, true, false));
                        }
                    }
                    $priceXmlObj->addAttribute(
                        'to_including_tax', $coreHelper->currency($maximalPriceInclTax, true, false)
                    );
                } else {
                    $priceXmlObj->addAttribute('to', $coreHelper->currency($maximalPriceTax, true, false));
                    if ($weeeTaxAmount && $product->getPriceType() == 1
                        && $weeeHelper->typeOfDisplay($product, array(2, 1, 4))
                    ) {
                        $weeeXmlObj = $priceXmlObj->addChild('to_weee');
                        $weeeSeparator = ' + ';
                        $weeeXmlObj->addAttribute('separator', $weeeSeparator);
                        foreach ($weeeTaxAttributes as $weeeTaxAttribute) {
                            if ($weeeHelper->typeOfDisplay($product, array(2, 4))) {
                                $amount = $weeeTaxAttribute->getAmount() + $weeeTaxAttribute->getTaxAmount();
                            } else {
                                $amount = $weeeTaxAttribute->getAmount();
                            }
                            $weeeItemXmlObj = $weeeXmlObj->addChild('item');
                            $weeeItemXmlObj->addAttribute(
                                'name', $weeeItemXmlObj->escapeXml($weeeTaxAttribute->getName())
                            );
                            $weeeItemXmlObj->addAttribute('amount', $coreHelper->currency($amount, true, false));
                        }
                    }
                    if ($weeeHelper->typeOfDisplay($product, 2) && $weeeTaxAmount) {
                        $priceXmlObj->addAttribute(
                            'to_including_tax', $coreHelper->currency($maximalPriceInclTax, true, false)
                        );
                    }
                }
            /**
             * if ($minimalPrice <> $maximalPrice) {
             */
            } else {
                if ($taxHelper->displayBothPrices()) {
                    $priceXmlObj->addAttribute('excluding_tax', $coreHelper->currency($minimalPriceTax, true, false));
                    if ($weeeTaxAmount && $product->getPriceType() == 1
                        && $weeeHelper->typeOfDisplay($product, array(2, 1, 4))
                    ) {
                        $weeeXmlObj = $priceXmlObj->addChild('weee');
                        $weeeSeparator = ' + ';
                        $weeeXmlObj->addAttribute('separator', $weeeSeparator);
                        foreach ($weeeTaxAttributes as $weeeTaxAttribute) {
                            if ($weeeHelper->typeOfDisplay($product, array(2, 4))) {
                                $amount = $weeeTaxAttribute->getAmount() + $weeeTaxAttribute->getTaxAmount();
                            } else {
                                $amount = $weeeTaxAttribute->getAmount();
                            }
                            $weeeItemXmlObj = $weeeXmlObj->addChild('item');
                            $weeeItemXmlObj->addAttribute(
                                'name', $weeeItemXmlObj->escapeXml($weeeTaxAttribute->getName())
                            );
                            $weeeItemXmlObj->addAttribute('amount', $coreHelper->currency($amount, true, false));
                        }
                    }
                    $priceXmlObj->addAttribute(
                        'including_tax',$coreHelper->currency($minimalPriceInclTax, true, false)
                    );
                } else {
                    $priceXmlObj->addAttribute('regular', $coreHelper->currency($minimalPriceTax, true, false));
                    if ($weeeTaxAmount && $product->getPriceType() == 1
                        && $weeeHelper->typeOfDisplay($product, array(2, 1, 4))
                    ) {
                        $weeeXmlObj = $priceXmlObj->addChild('weee');
                        $weeeSeparator = ' + ';
                        $weeeXmlObj->addAttribute('separator', $weeeSeparator);
                        foreach ($weeeTaxAttributes as $weeeTaxAttribute) {
                            if ($weeeHelper->typeOfDisplay($product, array(2, 4))) {
                                $amount = $weeeTaxAttribute->getAmount() + $weeeTaxAttribute->getTaxAmount();
                            } else {
                                $amount = $weeeTaxAttribute->getAmount();
                            }
                            $weeeItemXmlObj = $weeeXmlObj->addChild('item');
                            $weeeItemXmlObj->addAttribute(
                                'name', $weeeItemXmlObj->escapeXml($weeeTaxAttribute->getName())
                            );
                            $weeeItemXmlObj->addAttribute('amount', $coreHelper->currency($amount, true, false));
                        }
                    }
                    if ($weeeHelper->typeOfDisplay($product, 2) && $weeeTaxAmount) {
                        $priceXmlObj->addAttribute(
                            'including_tax', $coreHelper->currency($minimalPriceInclTax, true, false)
                        );
                    }
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
    protected function _getTierPrices($product)
    {
        if (is_null($product)) {
            return array();
        }
        $prices  = $product->getFormatedTierPrice();

        $res = array();
        if (is_array($prices)) {
            foreach ($prices as $price) {
                $price['price_qty'] = $price['price_qty']*1;
                $price['savePercent'] = ceil(100 - $price['price']);
                $price['formated_price'] = Mage::app()->getStore()->formatPrice(
                    Mage::app()->getStore()->convertPrice(
                        Mage::helper('tax')->getPrice($product, $price['website_price'])
                    ),
                    false
                );
                $price['formated_price_incl_tax'] = Mage::app()->getStore()->formatPrice(
                    Mage::app()->getStore()->convertPrice(
                        Mage::helper('tax')->getPrice($product, $price['website_price'], true)
                    ),
                    false
                );
                $res[] = $price;
            }
        }

        return $res;
    }
}
