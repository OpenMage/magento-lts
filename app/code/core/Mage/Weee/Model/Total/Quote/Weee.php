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
 * @package     Mage_Weee
 * @copyright  Copyright (c) 2006-2014 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Weee calculation model
 *
 * @category    Mage
 * @package     Mage_Weee
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Weee_Model_Total_Quote_Weee extends Mage_Tax_Model_Sales_Total_Quote_Tax
{
    /**
     * Weee module helper object
     *
     * @var Mage_Weee_Helper_Data
     */
    protected $_helper;

    /**
     * Store model
     *
     * @var Mage_Core_Model_Store
     */
    protected $_store;

    /**
     * Tax configuration object
     *
     * @var Mage_Tax_Model_Config
     */
    protected $_config;

    /**
     * Flag which notify what tax amount can be affected by fixed porduct tax
     *
     * @var bool
     */
    protected $_isTaxAffected;

    /**
     * Initialize Weee totals collector
     */
    public function __construct()
    {
        $this->setCode('weee');
        $this->_helper = Mage::helper('weee');
        $this->_config = Mage::getSingleton('tax/config');
    }

    /**
     * Collect Weee taxes amount and prepare items prices for taxation and discount
     *
     * @param   Mage_Sales_Model_Quote_Address $address
     * @return  Mage_Weee_Model_Total_Quote_Weee
     */
    public function collect(Mage_Sales_Model_Quote_Address $address)
    {
        Mage_Sales_Model_Quote_Address_Total_Abstract::collect($address);
        $this->_isTaxAffected = false;
        $items = $this->_getAddressItems($address);
        if (!count($items)) {
            return $this;
        }

        $address->setAppliedTaxesReset(true);
        $address->setAppliedTaxes(array());

        $this->_store = $address->getQuote()->getStore();
        $this->_helper->setStore($this->_store);

        foreach ($items as $item) {
            if ($item->getParentItemId()) {
                continue;
            }
            $this->_resetItemData($item);
            if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                foreach ($item->getChildren() as $child) {
                    $this->_resetItemData($child);
                    $this->_process($address, $child);
                }
                $this->_recalculateParent($item);
            } else {
                $this->_process($address, $item);
            }
        }

        if ($this->_isTaxAffected) {
            $address->unsSubtotalInclTax();
            $address->unsBaseSubtotalInclTax();
        }

        return $this;
    }

    /**
     * Calculate item fixed tax and prepare information for discount and recular taxation
     *
     * @param   Mage_Sales_Model_Quote_Address $address
     * @param   Mage_Sales_Model_Quote_Item_Abstract $item
     * @return  Mage_Weee_Model_Total_Quote_Weee
     */
    protected function _process(Mage_Sales_Model_Quote_Address $address, $item)
    {
        if (!$this->_helper->isEnabled($this->_store)) {
            return $this;
        }

        $attributes = $this->_helper->getProductWeeeAttributes(
            $item->getProduct(),
            $address,
            $address->getQuote()->getBillingAddress(),
            $this->_store->getWebsiteId()
        );

        $applied = array();
        $productTaxes = array();

        $totalValue = 0;
        $baseTotalValue = 0;
        $totalRowValue = 0;
        $baseTotalRowValue = 0;

        $totalExclTaxValue = 0;
        $baseTotalExclTaxValue = 0;
        $totalExclTaxRowValue = 0;
        $baseTotalExclTaxRowValue = 0;

        $customerRatePercentage = $this->_customerRatePercent($address,$item);

        foreach ($attributes as $k => $attribute) {
            $baseValue = $attribute->getAmount();
            $baseValueExclTax = $baseValue;

            if ($customerRatePercentage && $this->_helper->isTaxIncluded($this->_store)) {
                //Remove the customer tax. This in general applies to EU scenario
                $baseValueExclTax
                        = $this->_getCalculator()->round(($baseValue * 100) / (100 + $customerRatePercentage));
            }

            $value = $this->_store->convertPrice($baseValue);
            $rowValue = $value * $item->getTotalQty();
            $baseRowValue = $baseValue * $item->getTotalQty();

            //Get the values excluding tax
            $valueExclTax = $this->_store->convertPrice($baseValueExclTax);
            $rowValueExclTax = $valueExclTax * $item->getTotalQty();
            $baseRowValueExclTax = $baseValueExclTax * $item->getTotalQty();

            $title = $attribute->getName();

            //Calculate the Wee value
            $totalValue += $value;
            $baseTotalValue += $baseValue;
            $totalRowValue += $rowValue;
            $baseTotalRowValue += $baseRowValue;

            //Calculate the Wee without tax
            $totalExclTaxValue += $valueExclTax;
            $baseTotalExclTaxValue += $baseValueExclTax;
            $totalExclTaxRowValue += $rowValueExclTax;
            $baseTotalExclTaxRowValue += $baseRowValueExclTax;

            /*
             * Note: including Tax does not necessarily mean it includes all the tax
             * *_incl_tax only holds the tax associated with Tax included products
             */

            $productTaxes[] = array(
                'title' => $title,
                'base_amount' => $baseValueExclTax,
                'amount' => $valueExclTax,
                'row_amount' => $rowValueExclTax,
                'base_row_amount' => $baseRowValueExclTax,
                /**
                 * Tax value can't be presented as include/exclude tax
                 */
                'base_amount_incl_tax' => $baseValue,
                'amount_incl_tax' => $value,
                'row_amount_incl_tax' => $rowValue,
                'base_row_amount_incl_tax' => $baseRowValue,
            );

            $applied[] = array(
                'id' => $attribute->getCode(),
                'percent' => null,
                'hidden' => $this->_helper->includeInSubtotal($this->_store),
                'rates' => array(array(
                    'base_real_amount' => $baseRowValue,
                    'base_amount' => $baseRowValue,
                    'amount' => $rowValue,
                    'code' => $attribute->getCode(),
                    'title' => $title,
                    'percent' => null,
                    'position' => 1,
                    'priority' => -1000 + $k,
                ))
            );
        }

        //We set the TAX exclusive value
        $item->setWeeeTaxAppliedAmount($totalExclTaxValue);
        $item->setBaseWeeeTaxAppliedAmount($baseTotalExclTaxValue);
        $item->setWeeeTaxAppliedRowAmount($totalExclTaxRowValue);
        $item->setBaseWeeeTaxAppliedRowAmount($baseTotalExclTaxRowValue);

        $this->_processTaxSettings($item, $totalExclTaxValue, $baseTotalExclTaxValue,
            $totalExclTaxRowValue, $baseTotalExclTaxRowValue)
            ->_processTotalAmount($address, $totalExclTaxRowValue, $baseTotalExclTaxRowValue);

        $this->_helper->setApplied($item, array_merge($this->_helper->getApplied($item), $productTaxes));
        if ($applied) {
            $this->_saveAppliedTaxes($address, $applied,
                $item->getWeeeTaxAppliedAmount(),
                $item->getBaseWeeeTaxAppliedAmount(),
                null
            );
        }
    }

    /**
     * Get the default store rate
     *
     * @param Mage_Sales_Model_Quote_Address $address
     * @param Mage_Sales_Model_Quote_Item_Abstract $item
     * @return mixed
     */
    protected function _customerRatePercent($address, $item)
    {
        $taxCalculationModel = Mage::getSingleton('tax/calculation');

        $request = $taxCalculationModel->getRateRequest(
            $address,
            $address->getQuote()->getBillingAddress(),
            $address->getQuote()->getCustomerTaxClassId(),
            $this->_store
        );

        $customerRatePercentage = $taxCalculationModel->getRate(
            $request->setProductClassId($item->getProduct()->getTaxClassId())
        );
        return $customerRatePercentage;
    }

    /**
     * Check if discount should be applied to weee and add weee to discounted price
     *
     * @deprecated since 1.8
     * @param   Mage_Sales_Model_Quote_Item_Abstract $item
     * @param   float $value
     * @param   float $baseValue
     * @return  Mage_Weee_Model_Total_Quote_Weee
     */
    protected function _processDiscountSettings($item, $value, $baseValue)
    {
        if ($this->_helper->isDiscounted($this->_store)) {
            Mage::helper('salesrule')->addItemDiscountPrices($item, $baseValue, $value);
        }
        return $this;
    }

    /**
     * Add extra amount which should be taxable by regular tax
     *
     * @param   Mage_Sales_Model_Quote_Item_Abstract $item
     * @param   float $value
     * @param   float $baseValue
     * @param   float $rowValue
     * @param   float $baseRowValue
     * @return  Mage_Weee_Model_Total_Quote_Weee
     */
    protected function _processTaxSettings($item, $value, $baseValue, $rowValue, $baseRowValue)
    {
        if ($rowValue) {
            $this->_isTaxAffected = true;
            $item->unsRowTotalInclTax()
                ->unsBaseRowTotalInclTax()
                ->unsPriceInclTax()
                ->unsBasePriceInclTax();
        }
        if ($this->_helper->isTaxable($this->_store)
            && !$this->_helper->isTaxIncluded($this->_store) && $rowValue) {
            if (!$this->_helper->includeInSubtotal($this->_store)) {
                $item->setExtraTaxableAmount($value)
                    ->setBaseExtraTaxableAmount($baseValue)
                    ->setExtraRowTaxableAmount($rowValue)
                    ->setBaseExtraRowTaxableAmount($baseRowValue);
            }
        }
        return $this;
    }

    /**
     * Proces row amount based on FPT total amount configuration setting
     *
     * @param   Mage_Sales_Model_Quote_Address $address
     * @param   float $rowValue
     * @param   float $baseRowValue
     * @return  Mage_Weee_Model_Total_Quote_Weee
     */
    protected function _processTotalAmount($address, $rowValue, $baseRowValue)
    {
        if ($this->_helper->includeInSubtotal($this->_store)) {
            $address->addTotalAmount('subtotal', $rowValue);
            $address->addBaseTotalAmount('subtotal', $baseRowValue);
            $this->_isTaxAffected = true;
        } else {
            $address->setExtraTaxAmount($address->getExtraTaxAmount() + $rowValue);
            $address->setBaseExtraTaxAmount($address->getBaseExtraTaxAmount() + $baseRowValue);
        }
        return $this;
    }

    /**
     * Recalculate parent item amounts based on children results
     *
     * @param   Mage_Sales_Model_Quote_Item_Abstract $item
     * @return  Mage_Weee_Model_Total_Quote_Weee
     */
    protected function _recalculateParent(Mage_Sales_Model_Quote_Item_Abstract $item)
    {

    }

    /**
     * Reset information about FPT for shopping cart item
     *
     * @param   Mage_Sales_Model_Quote_Item_Abstract $item
     * @return  Mage_Weee_Model_Total_Quote_Weee
     */
    protected function _resetItemData($item)
    {
        $this->_helper->setApplied($item, array());

        $item->setBaseWeeeTaxDisposition(0);
        $item->setWeeeTaxDisposition(0);

        $item->setBaseWeeeTaxRowDisposition(0);
        $item->setWeeeTaxRowDisposition(0);

        $item->setBaseWeeeTaxAppliedAmount(0);
        $item->setBaseWeeeTaxAppliedRowAmount(0);

        $item->setWeeeTaxAppliedAmount(0);
        $item->setWeeeTaxAppliedRowAmount(0);
    }

    /**
     * Fetch FPT data to address object for display in totals block
     *
     * @param   Mage_Sales_Model_Quote_Address $address
     * @return  Mage_Weee_Model_Total_Quote_Weee
     */
    public function fetch(Mage_Sales_Model_Quote_Address $address)
    {
        return $this;
    }

    /**
     * Process model configuration array.
     * This method can be used for changing totals collect sort order
     *
     * @param   array $config
     * @param   store $store
     * @return  array
     */
    public function processConfigArray($config, $store)
    {
        return $config;
    }

    /**
     * Process item fixed taxes
     *
     * @deprecated since 1.3.2.3
     * @param   Mage_Sales_Model_Quote_Address $address
     * @param   Mage_Sales_Model_Quote_Item_Abstract $item
     * @param   bool $updateParent
     * @return  Mage_Weee_Model_Total_Quote_Weee
     */
    protected function _processItem(Mage_Sales_Model_Quote_Address $address, $item, $updateParent = false)
    {
        $store = $address->getQuote()->getStore();
        if (!$this->_helper->isEnabled($store)) {
            return $this;
        }
        $custTaxClassId = $address->getQuote()->getCustomerTaxClassId();

        $taxCalculationModel = Mage::getSingleton('tax/calculation');
        /* @var $taxCalculationModel Mage_Tax_Model_Calculation */
        $request = $taxCalculationModel->getRateRequest(
            $address,
            $address->getQuote()->getBillingAddress(),
            $custTaxClassId,
            $store
        );
        $defaultRateRequest = $taxCalculationModel->getDefaultRateRequest($store);

        $attributes = $this->_helper->getProductWeeeAttributes(
            $item->getProduct(),
            $address,
            $address->getQuote()->getBillingAddress(),
            $store->getWebsiteId()
        );

        $applied = array();
        $productTaxes = array();

        foreach ($attributes as $k => $attribute) {
            $baseValue = $attribute->getAmount();
            $value = $store->convertPrice($baseValue);

            $rowValue = $value*$item->getQty();
            $baseRowValue = $baseValue*$item->getQty();

            $title = $attribute->getName();

            /**
             * Apply discount to fixed tax
             */
            if ($item->getDiscountPercent() && $this->_helper->isDiscounted($store)) {
                $valueDiscount = $value/100*$item->getDiscountPercent();
                $baseValueDiscount = $baseValue/100*$item->getDiscountPercent();

                $rowValueDiscount = $rowValue/100*$item->getDiscountPercent();
                $baseRowValueDiscount = $baseRowValue/100*$item->getDiscountPercent();

                $address->setDiscountAmount($address->getDiscountAmount()+$rowValueDiscount);
                $address->setBaseDiscountAmount($address->getBaseDiscountAmount()+$baseRowValueDiscount);

                $address->setGrandTotal($address->getGrandTotal() - $rowValueDiscount);
                $address->setBaseGrandTotal($address->getBaseGrandTotal() - $baseRowValueDiscount);
            }

            $oneDisposition = $baseOneDisposition = $disposition = $baseDisposition = 0;

            /**
             * Apply tax percent to fixed tax
             */
            if ($this->_helper->isTaxable($store)) {
                $currentPercent = $item->getTaxPercent();
                $defaultPercent = $taxCalculationModel->getRate(
                    $defaultRateRequest->setProductClassId($item->getProduct()->getTaxClassId())
                );

                $valueBeforeVAT = $rowValue;
                $baseValueBeforeVAT = $baseRowValue;

                $oneDisposition = $store->roundPrice($value/(100+$defaultPercent)*$currentPercent);
                $baseOneDisposition = $store->roundPrice($baseValue/(100+$defaultPercent)*$currentPercent);

                $disposition = $store->roundPrice($rowValue/(100+$defaultPercent)*$currentPercent);
                $baseDisposition = $store->roundPrice($baseRowValue/(100+$defaultPercent)*$currentPercent);

                $item->setBaseTaxAmount($item->getBaseTaxAmount()+$baseDisposition);
                $item->setTaxAmount($item->getTaxAmount()+$disposition);

                $value -= $oneDisposition;
                $baseValue -= $baseOneDisposition;

                $rowValue -= $baseDisposition;
                $baseRowValue -= $disposition;

                $item->setWeeeTaxDisposition($item->getWeeeTaxDisposition() + $oneDisposition);
                $item->setBaseWeeeTaxDisposition($item->getBaseWeeeTaxDisposition() + $baseOneDisposition);
                $item->setWeeeTaxRowDisposition($item->getWeeeTaxRowDisposition() + $disposition);
                $item->setBaseWeeeTaxRowDisposition($item->getBaseWeeeTaxRowDisposition() + $baseDisposition);

//                $item->setTaxBeforeDiscount($item->getTaxBeforeDiscount() + $disposition);
//                $item->setBaseTaxBeforeDiscount($item->getBaseTaxBeforeDiscount() + $baseDisposition);

                $address->setTaxAmount($address->getTaxAmount() + $disposition);
                $address->setBaseTaxAmount($address->getBaseTaxAmount() + $baseDisposition);

                $rate = $taxCalculationModel->getRate(
                    $request->setProductClassId($item->getProduct()->getTaxClassId())
                );

                $this->_saveAppliedTaxes(
                   $address,
                   $taxCalculationModel->getAppliedRates($request),
                   $store->roundPrice($valueBeforeVAT-$rowValue),
                   $store->roundPrice($baseValueBeforeVAT-$baseRowValue),
                   $rate
                );

                $address->setGrandTotal(
                    $address->getGrandTotal() + $store->roundPrice($valueBeforeVAT - $rowValue)
                );
                $address->setBaseGrandTotal(
                    $address->getBaseGrandTotal() + $store->roundPrice($baseValueBeforeVAT - $baseRowValue)
                );
            }

            /**
             * Check if need include fixed tax amount to subtotal
             */
            if ($this->_helper->includeInSubtotal($store)) {
                $address->setSubtotal($address->getSubtotal() + $rowValue);
                $address->setBaseSubtotal($address->getBaseSubtotal() + $baseRowValue);

//                $address->setSubtotalWithDiscount($address->getSubtotalWithDiscount() + $rowValue);
//                $address->setBaseSubtotalWithDiscount($address->getBaseSubtotalWithDiscount() + $baseRowValue);
            } else {
                $address->setTaxAmount($address->getTaxAmount() + $rowValue);
                $address->setBaseTaxAmount($address->getBaseTaxAmount() + $baseRowValue);
            }


            $productTaxes[] = array(
                'title' => $title,
                'base_amount' => $baseValue,
                'amount' => $value,

                'row_amount' => $rowValue,
                'base_row_amount' => $baseRowValue,

                'base_amount_incl_tax' => $baseValue+$baseOneDisposition,
                'amount_incl_tax' => $value+$oneDisposition,

                'row_amount_incl_tax' => $rowValue+$disposition,
                'base_row_amount_incl_tax' => $baseRowValue+$baseDisposition,
            );

            $applied[] = array(
                'id' => $attribute->getCode(),
                'percent' => null,
                'hidden' => $this->_helper->includeInSubtotal($store),
                'rates' => array(array(
                    'amount' => $rowValue,
                    'base_amount' => $baseRowValue,
                    'base_real_amount' => $baseRowValue,
                    'code' => $attribute->getCode(),
                    'title' => $title,
                    'percent' => null,
                    'position' => 1,
                    'priority' => -1000 + $k,
                ))
            );

            $item->setBaseWeeeTaxAppliedAmount($item->getBaseWeeeTaxAppliedAmount() + $baseValue);
            $item->setBaseWeeeTaxAppliedRowAmount($item->getBaseWeeeTaxAppliedRowAmount() + $baseRowValue);

            $item->setWeeeTaxAppliedAmount($item->getWeeeTaxAppliedAmount() + $value);
            $item->setWeeeTaxAppliedRowAmount($item->getWeeeTaxAppliedRowAmount() + $rowValue);
        }

        $this->_helper->setApplied($item, array_merge($this->_helper->getApplied($item), $productTaxes));

        if ($updateParent) {
            $parent = $item->getParentItem();

            $parent->setBaseWeeeTaxDisposition(
                $parent->getBaseWeeeTaxDisposition() + $item->getBaseWeeeTaxDisposition()
            );
            $parent->setWeeeTaxDisposition(
                $parent->getWeeeTaxDisposition() + $item->getWeeeTaxDisposition()
            );

            $parent->setBaseWeeeTaxRowDisposition(
                $parent->getBaseWeeeTaxRowDisposition() + $item->getBaseWeeeTaxRowDisposition()
            );
            $parent->setWeeeTaxRowDisposition(
                $parent->getWeeeTaxRowDisposition() + $item->getWeeeTaxRowDisposition()
            );

            $parent->setBaseWeeeTaxAppliedAmount(
                $parent->getBaseWeeeTaxAppliedAmount() + $item->getBaseWeeeTaxAppliedAmount()
            );
            $parent->setBaseWeeeTaxAppliedRowAmount(
                $parent->getBaseWeeeTaxAppliedRowAmount() + $item->getBaseWeeeTaxAppliedRowAmount()
            );

            $parent->setWeeeTaxAppliedAmount(
                $parent->getWeeeTaxAppliedAmount() + $item->getWeeeTaxAppliedAmount()
            );
            $parent->setWeeeTaxAppliedRowAmount(
                $parent->getWeeeTaxAppliedRowAmount() + $item->getWeeeTaxAppliedRowAmount()
            );
        }

        if ($applied) {
            $this->_saveAppliedTaxes(
               $address,
               $applied,
               $item->getWeeeTaxAppliedAmount(),
               $item->getBaseWeeeTaxAppliedAmount(),
               null
            );
        }
    }

    /**
     * Returns the model for calculation
     *
     * @return Mage_Tax_Model_Calculation
     */
    protected function _getCalculator()
    {
        return Mage::getSingleton('tax/calculation');
    }

    /**
     * Set the helper object.
     *
     * @param Mage_Weee_Helper_Data $helper
     */
    public function setHelper($helper)
    {
        $this->_helper = $helper;
    }


    /**
     * Set the store Object
     *
     * @param  Mage_Core_Model_Store $store
     */
    public function setStore($store)
    {
        $this->_store = $store;
    }

    /**
     * No aggregated label for fixed product tax
     *
     * TODO: fix
     */
    public function getLabel()
    {
        return '';
    }
}
