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
 * Default product price xml renderer
 *
 * @category   Mage
 * @package    Mage_XmlConnect
 * @author     Magento Core Team <core@magentocommerce.com>
 */

class Mage_XmlConnect_Block_Catalog_Product_Price_Default extends Mage_Catalog_Block_Product_Price
{
    /**
     * Collect product prices to specified item xml object
     *
     * @param Mage_Catalog_Model_Product $product
     * @param Mage_XmlConnect_Model_Simplexml_Element $item
     */
    public function collectProductPrices(Mage_Catalog_Model_Product $product, Mage_XmlConnect_Model_Simplexml_Element $item)
    {
        $this->setProduct($product)
            ->setDisplayMinimalPrice(true)
            ->setUseLinkForAsLowAs(false);

        $priceXmlObj = $item->addChild('price');
        $_tierPrices = $this->_getTierPrices($product);
        if (count($_tierPrices) > 0) {
            $tierPricesTextArray = array();
            $tierPricesTextArray = $this->_getTierPricesTextArray($_tierPrices, $product);
            $item->addChild('price_tier', implode("\n", $tierPricesTextArray));
        }

        $_coreHelper = $this->helper('core');
        $_weeeHelper = $this->helper('weee');
        $_taxHelper  = $this->helper('tax');

        /* @var $_coreHelper Mage_Core_Helper_Data */
        /* @var $_weeeHelper Mage_Weee_Helper_Data */
        /* @var $_taxHelper Mage_Tax_Helper_Data */

        $_id = $product->getId();
        $_weeeSeparator = '';
        $_simplePricesTax = ($_taxHelper->displayPriceIncludingTax() || $_taxHelper->displayBothPrices());
        $_minimalPriceValue = $product->getMinimalPrice();
        $_minimalPrice = $_taxHelper->getPrice($product, $_minimalPriceValue, $_simplePricesTax);

        if (!$product->isGrouped()) {
            $_weeeTaxAmount = $_weeeHelper->getAmountForDisplay($product);
            if ($_weeeHelper->typeOfDisplay($product, array(1,2,4))) {
                $_weeeTaxAmount = $_weeeHelper->getAmount($product);
                $_weeeTaxAttributes = $_weeeHelper->getProductWeeeAttributesForDisplay($product);
            }

            $_price = $_taxHelper->getPrice($product, $product->getPrice());
            $_regularPrice = $_taxHelper->getPrice($product, $product->getPrice(), $_simplePricesTax);
            $_finalPrice = $_taxHelper->getPrice($product, $product->getFinalPrice());
            $_finalPriceInclTax = $_taxHelper->getPrice($product, $product->getFinalPrice(), true);
            $_weeeDisplayType = $_weeeHelper->getPriceDisplayType();
            if ($_finalPrice == $_price) {
                if ($_taxHelper->displayBothPrices()) {
                    /**
                     * Including
                     */
                    if ($_weeeTaxAmount && $_weeeHelper->typeOfDisplay($product, 0)) {
                        $priceXmlObj->addAttribute('excluding_tax', $_coreHelper->currency($_price + $_weeeTaxAmount, true, false));
                        $priceXmlObj->addAttribute('including_tax', $_coreHelper->currency($_finalPriceInclTax + $_weeeTaxAmount, true, false));
                    } elseif ($_weeeTaxAmount && $_weeeHelper->typeOfDisplay($product, 1)) {
                        /**
                         * Including + Weee
                         */
                        $priceXmlObj->addAttribute('excluding_tax', $_coreHelper->currency($_price + $_weeeTaxAmount, true, false));
                        $priceXmlObj->addAttribute('including_tax', $_coreHelper->currency($_finalPriceInclTax + $_weeeTaxAmount, true, false));
                        $weeeXmlObj = $priceXmlObj->addChild('weee');
                        $_weeeSeparator = ' + ';
                        $weeeXmlObj->addAttribute('separator', $_weeeSeparator);
                        foreach ($_weeeTaxAttributes as $_weeeTaxAttribute) {
                            $weeeItemXmlObj = $weeeXmlObj->addChild('item');
                            $weeeItemXmlObj->addAttribute('name', $weeeItemXmlObj->xmlentities(strip_tags($_weeeTaxAttribute->getName())));
                            $weeeItemXmlObj->addAttribute('amount', $_coreHelper->currency($_weeeTaxAttribute->getAmount(), true, false));
                        }
                    } elseif ($_weeeTaxAmount && $_weeeHelper->typeOfDisplay($product, 4)) {
                        /**
                         * Including + Weee
                         */
                        $priceXmlObj->addAttribute('excluding_tax', $_coreHelper->currency($_price + $_weeeTaxAmount, true, false));
                        $priceXmlObj->addAttribute('including_tax', $_coreHelper->currency($_finalPriceInclTax + $_weeeTaxAmount, true, false));
                        $weeeXmlObj = $priceXmlObj->addChild('weee');
                        $_weeeSeparator = ' + ';
                        $weeeXmlObj->addAttribute('separator', $_weeeSeparator);
                        foreach ($_weeeTaxAttributes as $_weeeTaxAttribute) {
                            $weeeItemXmlObj = $weeeXmlObj->addChild('item');
                            $weeeItemXmlObj->addAttribute('name', $weeeItemXmlObj->xmlentities(strip_tags($_weeeTaxAttribute->getName())));
                            $weeeItemXmlObj->addAttribute('amount', $_coreHelper->currency($_weeeTaxAttribute->getAmount() + $_weeeTaxAttribute->getTaxAmount(), true, false));
                        }
                    } elseif ($_weeeTaxAmount && $_weeeHelper->typeOfDisplay($product, 2)) {
                        /**
                         * Excluding + Weee + Final
                         */
                        $priceXmlObj->addAttribute('excluding_tax', $_coreHelper->currency($_price, true, false));
                        $weeeXmlObj = $priceXmlObj->addChild('weee');
                        foreach ($_weeeTaxAttributes as $_weeeTaxAttribute) {
                            $weeeItemXmlObj = $weeeXmlObj->addChild('item');
                            $weeeItemXmlObj->addAttribute('name', $weeeItemXmlObj->xmlentities(strip_tags($_weeeTaxAttribute->getName())));
                            $weeeItemXmlObj->addAttribute('amount', $_coreHelper->currency($_weeeTaxAttribute->getAmount(), true, false));
                        }
                        $priceXmlObj->addAttribute('including_tax', $_coreHelper->currency($_finalPriceInclTax + $_weeeTaxAmount, true, false));
                    } else {
                         $priceXmlObj->addAttribute('excluding_tax', $_coreHelper->currency($_price, true, false));
                        $priceXmlObj->addAttribute('including_tax', $_coreHelper->currency($_finalPriceInclTax, true, false));
                    }
                /**
                 * if ($_taxHelper->displayBothPrices()) {
                 */
                } else {
                    /**
                     * Including
                     */
                    if ($_weeeTaxAmount && $_weeeHelper->typeOfDisplay($product, 0)) {
                        $priceXmlObj->addAttribute('regular', $_coreHelper->currency($_price + $_weeeTaxAmount, true, false));
                    } elseif ($_weeeTaxAmount && $_weeeHelper->typeOfDisplay($product, 1)) {
                        /**
                         * Including + Weee
                         */

                        $priceXmlObj->addAttribute('regular', $_coreHelper->currency($_price + $_weeeTaxAmount, true, false));
                        $weeeXmlObj = $priceXmlObj->addChild('weee');
                        $_weeeSeparator = ' + ';
                        $weeeXmlObj->addAttribute('separator', $_weeeSeparator);
                        foreach ($_weeeTaxAttributes as $_weeeTaxAttribute) {
                            $weeeItemXmlObj = $weeeXmlObj->addChild('item');
                            $weeeItemXmlObj->addAttribute('name', $weeeItemXmlObj->xmlentities(strip_tags($_weeeTaxAttribute->getName())));
                            $weeeItemXmlObj->addAttribute('amount', $_coreHelper->currency($_weeeTaxAttribute->getAmount(), true, false));
                        }
                    } elseif ($_weeeTaxAmount && $_weeeHelper->typeOfDisplay($product, 4)) {
                    /**
                     * Including + Weee
                     */
                        $priceXmlObj->addAttribute('regular', $_coreHelper->currency($_price + $_weeeTaxAmount, true, false));
                        $weeeXmlObj = $priceXmlObj->addChild('weee');
                        $_weeeSeparator = ' + ';
                        $weeeXmlObj->addAttribute('separator', $_weeeSeparator);
                        foreach ($_weeeTaxAttributes as $_weeeTaxAttribute) {
                            $weeeItemXmlObj = $weeeXmlObj->addChild('item');
                            $weeeItemXmlObj->addAttribute('name', $weeeItemXmlObj->xmlentities(strip_tags($_weeeTaxAttribute->getName())));
                            $weeeItemXmlObj->addAttribute('amount', $_coreHelper->currency($_weeeTaxAttribute->getAmount() + $_weeeTaxAttribute->getTaxAmount(), true, false));
                        }
                    } elseif ($_weeeTaxAmount && $_weeeHelper->typeOfDisplay($product, 2)) {
                        /**
                         * Excluding + Weee + Final
                         */
                        $priceXmlObj->addAttribute('regular', $_coreHelper->currency($_price, true, false));
                        $weeeXmlObj = $priceXmlObj->addChild('weee');
                        foreach ($_weeeTaxAttributes as $_weeeTaxAttribute) {
                            $weeeItemXmlObj = $weeeXmlObj->addChild('item');
                            $weeeItemXmlObj->addAttribute('name', $weeeItemXmlObj->xmlentities(strip_tags($_weeeTaxAttribute->getName())));
                            $weeeItemXmlObj->addAttribute('amount', $_coreHelper->currency($_weeeTaxAttribute->getAmount(), true, false));
                        }
                        $priceXmlObj->addAttribute('including_tax', $_coreHelper->currency($_price + $_weeeTaxAmount, true, false));
                    } else {
                         $priceXmlObj->addAttribute('regular', $_coreHelper->currency($_price, true, false));
                    }
                }
            /**
             * if ($_finalPrice == $_price) {
             */
            } else {
                $_originalWeeeTaxAmount = $_weeeHelper->getOriginalAmount($product);
                /**
                 * Including
                 */
                if ($_weeeTaxAmount && $_weeeHelper->typeOfDisplay($product, 0)) {
                    $priceXmlObj->addAttribute('regular', $_coreHelper->currency($_regularPrice + $_originalWeeeTaxAmount, true, false));
                    if ($_taxHelper->displayBothPrices()) {
                        $priceXmlObj->addAttribute('special_excluding_tax', $_coreHelper->currency($_finalPrice + $_weeeTaxAmount, true, false));
                        $priceXmlObj->addAttribute('special_including_tax', $_coreHelper->currency($_finalPriceInclTax + $_weeeTaxAmount, true, false));
                    } else {
                         $priceXmlObj->addAttribute('special', $_coreHelper->currency($_finalPrice + $_weeeTaxAmount, true, false));
                    }
                } elseif ($_weeeTaxAmount && $_weeeHelper->typeOfDisplay($product, 1)) {
                    /**
                     * Including + Weee
                     */
                    $priceXmlObj->addAttribute('regular', $_coreHelper->currency($_regularPrice + $_originalWeeeTaxAmount, true, false));
                    $priceXmlObj->addAttribute('special_excluding_tax', $_coreHelper->currency($_finalPrice + $_weeeTaxAmount, true, false));
                    $weeeXmlObj = $priceXmlObj->addChild('weee');
                    $_weeeSeparator = ' + ';
                    $weeeXmlObj->addAttribute('separator', $_weeeSeparator);
                    foreach ($_weeeTaxAttributes as $_weeeTaxAttribute) {
                        $weeeItemXmlObj = $weeeXmlObj->addChild('item');
                        $weeeItemXmlObj->addAttribute('name', $weeeItemXmlObj->xmlentities(strip_tags($_weeeTaxAttribute->getName())));
                        $weeeItemXmlObj->addAttribute('amount', $_coreHelper->currency($_weeeTaxAttribute->getAmount(), true, false));
                    }
                    $priceXmlObj->addAttribute('special_including_tax', $_coreHelper->currency($_finalPriceInclTax + $_weeeTaxAmount, true, false));
                } elseif ($_weeeTaxAmount && $_weeeHelper->typeOfDisplay($product, 4)) {
                    /**
                     * Including + Weee
                     */
                    $priceXmlObj->addAttribute('regular', $_coreHelper->currency($_regularPrice + $_originalWeeeTaxAmount, true, false));
                    $priceXmlObj->addAttribute('special_excluding_tax', $_coreHelper->currency($_finalPrice + $_weeeTaxAmount, true, false));
                    $weeeXmlObj = $priceXmlObj->addChild('weee');
                    $_weeeSeparator = ' + ';
                    $weeeXmlObj->addAttribute('separator', $_weeeSeparator);
                    foreach ($_weeeTaxAttributes as $_weeeTaxAttribute) {
                        $weeeItemXmlObj = $weeeXmlObj->addChild('item');
                        $weeeItemXmlObj->addAttribute('name', $weeeItemXmlObj->xmlentities(strip_tags($_weeeTaxAttribute->getName())));
                        $weeeItemXmlObj->addAttribute('amount', $_coreHelper->currency($_weeeTaxAttribute->getAmount() + $_weeeTaxAttribute->getTaxAmount(), true, false));
                    }
                    $priceXmlObj->addAttribute('special_including_tax', $_coreHelper->currency($_finalPriceInclTax + $_weeeTaxAmount, true, false));
                } elseif ($_weeeTaxAmount && $_weeeHelper->typeOfDisplay($product, 2)) {
                    /**
                     * Excluding + Weee + Final
                     */
                    $priceXmlObj->addAttribute('regular', $_coreHelper->currency($_regularPrice, true, false));
                    $priceXmlObj->addAttribute('special_excluding_tax', $_coreHelper->currency($_finalPrice, true, false));
                    $weeeXmlObj = $priceXmlObj->addChild('weee');
                    foreach ($_weeeTaxAttributes as $_weeeTaxAttribute) {
                        $weeeItemXmlObj = $weeeXmlObj->addChild('item');
                        $weeeItemXmlObj->addAttribute('name', $weeeItemXmlObj->xmlentities(strip_tags($_weeeTaxAttribute->getName())));
                        $weeeItemXmlObj->addAttribute('amount', $_coreHelper->currency($_weeeTaxAttribute->getAmount(), true, false));
                    }
                    $priceXmlObj->addAttribute('special_including_tax', $_coreHelper->currency($_finalPriceInclTax + $_weeeTaxAmount, true, false));
                } else {
                    /**
                     * Excluding
                     */
                    $priceXmlObj->addAttribute('regular', $_coreHelper->currency($_regularPrice, true, false));
                    if ($_taxHelper->displayBothPrices()) {
                        $priceXmlObj->addAttribute('special_excluding_tax', $_coreHelper->currency($_finalPrice, true, false));
                        $priceXmlObj->addAttribute('special_including_tax', $_coreHelper->currency($_finalPriceInclTax, true, false));
                    } else {
                         $priceXmlObj->addAttribute('special', $_coreHelper->currency($_finalPrice, true, false));
                    }
                }
            }

            if ($this->getDisplayMinimalPrice() && $_minimalPriceValue && $_minimalPriceValue < $product->getFinalPrice()) {
                $_minimalPriceDisplayValue = $_minimalPrice;
                if ($_weeeTaxAmount && $_weeeHelper->typeOfDisplay($product, array(0, 1, 4))) {
                    $_minimalPriceDisplayValue = $_minimalPrice + $_weeeTaxAmount;
                }

                if (!$this->getUseLinkForAsLowAs()) {
                    $priceXmlObj->addAttribute('as_low_as', $_coreHelper->currency($_minimalPriceDisplayValue, true, false));
                }
            }
        /**
         * if (!$product->isGrouped()) {
         */
        } else {
            $_exclTax = $_taxHelper->getPrice($product, $_minimalPriceValue, $includingTax = null);
            $_inclTax = $_taxHelper->getPrice($product, $_minimalPriceValue, $includingTax = true);

            if ($this->getDisplayMinimalPrice() && $_minimalPriceValue) {
                if ($_taxHelper->displayBothPrices()) {
                    $priceXmlObj->addAttribute('starting_at_excluding_tax', $_coreHelper->currency($_exclTax, true, false));
                    $priceXmlObj->addAttribute('starting_at_including_tax', $_coreHelper->currency($_inclTax, true, false));
                } else {
                    $_showPrice = $_inclTax;
                    if (!$_taxHelper->displayPriceIncludingTax()) {
                        $_showPrice = $_exclTax;
                    }
                    $priceXmlObj->addAttribute('starting_at', $_coreHelper->currency($_showPrice, true, false));
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
                    if ($price['price']<$product->getFinalPrice()) {
                        $price['savePercent'] = ceil(100 - (( 100/$product->getFinalPrice() ) * $price['price'] ));
                        $price['formated_price'] = Mage::app()->getStore()->formatPrice(Mage::app()->getStore()->convertPrice(Mage::helper('tax')->getPrice($product, $price['website_price'])), false);
                        $price['formated_price_incl_tax'] = Mage::app()->getStore()->formatPrice(Mage::app()->getStore()->convertPrice(Mage::helper('tax')->getPrice($product, $price['website_price'], true)), false);
                        $res[] = $price;
                    }
                } else {
                    if ($price['price']<$product->getPrice()) {
                        $price['savePercent'] = ceil(100 - (( 100/$product->getPrice() ) * $price['price'] ));
                        $price['formated_price'] = Mage::app()->getStore()->formatPrice(Mage::app()->getStore()->convertPrice(Mage::helper('tax')->getPrice($product, $price['website_price'])), false);
                        $price['formated_price_incl_tax'] = Mage::app()->getStore()->formatPrice(Mage::app()->getStore()->convertPrice(Mage::helper('tax')->getPrice($product, $price['website_price'], true)), false);
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
     * @param array $_tierPrices
     * @param Mage_Catalog_Model_Product $_product
     * 
     * @return array
     */
    protected function _getTierPricesTextArray($_tierPrices, $_product)
    {

        $pricesArray = array();
        if (Mage::helper('weee')->typeOfDisplay($_product, array(1, 2, 4))) {
            $_weeeTaxAttributes = Mage::helper('weee')->getProductWeeeAttributesForDisplay($_product);
        }

        if ($_product->isGrouped()) {
            $_tierPrices = $this->getTierPrices($_product);
        }
        Mage::helper('weee')->processTierPrices($_product, $_tierPrices);

        foreach ($_tierPrices as $_price) {
            $s = '';
            if ($this->helper('tax')->displayBothPrices()) {
                if (Mage::helper('weee')->typeOfDisplay($_product, 0)) {
                    $s .= $this->__('Buy %1$s for %2$s (%3$s incl. tax) each', $_price['price_qty'], $_price['formated_price_incl_weee_only'], $_price['formated_price_incl_weee']);
                } else if (Mage::helper('weee')->typeOfDisplay($_product, 1)) {
                    $s .= $this->__('Buy %1$s for %2$s', $_price['price_qty'], $_price['formated_price_incl_weee_only']);
                    if ($_weeeTaxAttributes) {
                        $s .= '(';
                        $s .= $this->__('%1$s incl tax.', $_price['formated_price_incl_weee']);
                        $separator = ' + ';
                        foreach ($_weeeTaxAttributes as $_attribute) {
                            $s .= $separator;
                            $s .= $_attribute->getName() . ': ' . Mage::helper('core')->currency($_attribute->getAmount());
                        }
                        $s .= ')';
                    }
                    $s .= $this->__('each');
                } else if (Mage::helper('weee')->typeOfDisplay($_product, 4)) {
                    $s .= $this->__('Buy %1$s for %2$s', $_price['price_qty'], $_price['formated_price_incl_weee_only']);
                    if ($_weeeTaxAttributes) {
                        $s .= '(';
                        $s .= $this->__('%1$s incl tax.', $_price['formated_price_incl_weee']);
                        $separator = ' + ';
                        foreach ($_weeeTaxAttributes as $_attribute) {
                            $s .= $separator;
                            $s .= $_attribute->getName() . ': ' . Mage::helper('core')->currency($_attribute->getAmount() + $_attribute->getTaxAmount());
                        }
                        $s .= ')';
                    }
                    $s .= $this->__('each');
                } else if (Mage::helper('weee')->typeOfDisplay($_product, 2)) {
                    $s .= $this->__('Buy %1$s for %2$s', $_price['price_qty'], $_price['formated_price']);
                    if ($_weeeTaxAttributes) {
                        $s .= '(';
                        foreach ($_weeeTaxAttributes as $_attribute) {
                            $s .= $_attribute->getName() . ': ' . Mage::helper('core')->currency($_attribute->getAmount());
                        }
                        $s .= $this->__('Total incl. Tax: %1$s', $_price['formated_price_incl_weee']);
                        $s .= ')';
                    }
                    $s .= $this->__('each');
                } else {
                    $s .= $this->__('Buy %1$s for %2$s (%3$s incl. tax) each', $_price['price_qty'], $_price['formated_price'], $_price['formated_price_incl_tax']);
                }
            } else {
                if ($this->helper('tax')->displayPriceIncludingTax()) {
                    if (Mage::helper('weee')->typeOfDisplay($_product, 0)) {
                        $s .= $this->__('Buy %1$s for %2$s each', $_price['price_qty'], $_price['formated_price_incl_weee']);
                    } else if (Mage::helper('weee')->typeOfDisplay($_product, 1)) {
                        $s .= $this->__('Buy %1$s for %2$s', $_price['price_qty'], $_price['formated_price_incl_weee']);
                        if ($_weeeTaxAttributes) {
                            $s .= '(';
                            $separator = '';
                            foreach ($_weeeTaxAttributes as $_attribute) {
                                $s .= $separator;
                                $s .= $_attribute->getName() . ': ' . Mage::helper('core')->currency($_attribute->getAmount());
                                $separator = ' + ';
                            }
                            $s .= ')';
                        }
                        $s .= $this->__('each');
                    } else if (Mage::helper('weee')->typeOfDisplay($_product, 4)) {
                        $s .= $this->__('Buy %1$s for %2$s', $_price['price_qty'], $_price['formated_price_incl_weee']);
                        if ($_weeeTaxAttributes) {
                            $s .= '(';
                            $separator = '';
                            foreach ($_weeeTaxAttributes as $_attribute) {
                                $s .= $separator;
                                $s .= $_attribute->getName() . ': ' . Mage::helper('core')->currency($_attribute->getAmount() + $_attribute->getTaxAmount());
                                $separator = ' + ';
                            }
                            $s .= ')';
                        }
                        $s .= $this->__('each');
                    } else if (Mage::helper('weee')->typeOfDisplay($_product, 2)) {
                        $s .= $this->__('Buy %1$s for %2$s', $_price['price_qty'], $_price['formated_price_incl_tax']);
                        if ($_weeeTaxAttributes) {
                            $s .= '(';
                            foreach ($_weeeTaxAttributes as $_attribute) {
                                $s .= $_attribute->getName() . ': ' . Mage::helper('core')->currency($_attribute->getAmount());
                            }
                            $s .= $this->__('Total incl. Tax: %1$s', $_price['formated_price_incl_weee']);
                            $s .= ')';
                        }
                        $s .= $this->__('each');
                    } else {
                        $s .= $this->__('Buy %1$s for %2$s each', $_price['price_qty'], $_price['formated_price_incl_tax']);
                    }
                } else {
                    if (Mage::helper('weee')->typeOfDisplay($_product, 0)) {
                        $s .= $this->__('Buy %1$s for %2$s each', $_price['price_qty'], $_price['formated_price_incl_weee_only']);
                    } else if (Mage::helper('weee')->typeOfDisplay($_product, 1)) {
                        $s .= $this->__('Buy %1$s for %2$s', $_price['price_qty'], $_price['formated_price_incl_weee_only']);
                        if ($_weeeTaxAttributes) {
                            $s .= '(';
                            $separator = '';
                            foreach ($_weeeTaxAttributes as $_attribute) {
                                $s .= $separator;
                                $s .= $_attribute->getName() . ': ' . Mage::helper('core')->currency($_attribute->getAmount());
                                $separator = ' + ';
                            }
                            $s .= ')';
                        }
                        $s .= $this->__('each');
                    } else if (Mage::helper('weee')->typeOfDisplay($_product, 4)) {
                        $s .= $this->__('Buy %1$s for %2$s', $_price['price_qty'], $_price['formated_price_incl_weee_only']);
                        if ($_weeeTaxAttributes) {
                            $s .= '(';
                            $separator = '';
                            foreach ($_weeeTaxAttributes as $_attribute) {
                                $s .= $separator;
                                $s .= $_attribute->getName() . ': ' . Mage::helper('core')->currency($_attribute->getAmount() + $_attribute->getTaxAmount());
                                $separator = ' + ';
                            }
                            $s .= ')';
                        }
                        $s .= $this->__('each');
                    } else if (Mage::helper('weee')->typeOfDisplay($_product, 2)) {
                        $s .= $this->__('Buy %1$s for %2$s', $_price['price_qty'], $_price['formated_price']);
                        if ($_weeeTaxAttributes) {
                            $s .= '(';
                            foreach ($_weeeTaxAttributes as $_attribute) {
                                $s .= $_attribute->getName() . ': ' . Mage::helper('core')->currency($_attribute->getAmount());
                            }
                            $s .= $this->__('Total incl. Tax: %1$s', $_price['formated_price_incl_weee_only']);
                            $s .= ')';
                        }
                        $s .= $this->__('each');
                    } else {
                        $s .= $this->__('Buy %1$s for %2$s each', $_price['price_qty'], $_price['formated_price']);
                    }
                }
            }
            if (!$_product->isGrouped()) {
                if (($_product->getPrice() == $_product->getFinalPrice() && $_product->getPrice() > $_price['price'])
                        || ($_product->getPrice() != $_product->getFinalPrice() && $_product->getFinalPrice() > $_price['price'])) {
                    $s .= ' ' . $this->__('and') . ' ' . $this->__('save') . ' ' . $_price['savePercent'] . '%';
                }
            }
            $pricesArray[] = $s;
        }
        return $pricesArray;
    }
}
