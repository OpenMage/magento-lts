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
 * @category   Mage
 * @package    Mage_Weee
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Mage_Weee_Model_Total_Quote_Weee extends Mage_Sales_Model_Quote_Address_Total_Tax
{
    public function __construct(){
        $this->setCode('weee');
    }

    public function collect(Mage_Sales_Model_Quote_Address $address)
    {
        $totalWeeeTax = 0;
        $baseTotalWeeeTax = 0;

        $items = $address->getAllItems();
        if (!count($items)) {
            return $this;
        }

        foreach ($items as $item) {
            if ($item->getParentItemId()) {
                continue;
            }

            $this->_resetItemData($item);

            if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                foreach ($item->getChildren() as $child) {
                    $this->_resetItemData($child);
                    $this->_processItem($address, $child, true);

                    $totalWeeeTax += $child->getWeeeTaxAppliedRowAmount();
                    $baseTotalWeeeTax += $child->getBaseWeeeTaxAppliedRowAmount();
                }
            } else {
                $this->_processItem($address, $item);

                $totalWeeeTax += $item->getWeeeTaxAppliedRowAmount();
                $baseTotalWeeeTax += $item->getBaseWeeeTaxAppliedRowAmount();
            }
        }

        $address->setGrandTotal($address->getGrandTotal() + $totalWeeeTax);
        $address->setBaseGrandTotal($address->getBaseGrandTotal() + $baseTotalWeeeTax);
        return $this;
    }

    protected function _processItem(Mage_Sales_Model_Quote_Address $address, $item, $updateParent = false)
    {
        $custTaxClassId = $address->getQuote()->getCustomerTaxClassId();
        $store = $address->getQuote()->getStore();

        $taxCalculationModel = Mage::getSingleton('tax/calculation');
        /* @var $taxCalculationModel Mage_Tax_Model_Calculation */
        $request = $taxCalculationModel->getRateRequest($address, $address->getQuote()->getBillingAddress(), $custTaxClassId, $store);
        $defaultRateRequest = $taxCalculationModel->getRateRequest(false, false, false, $store);

        $attributes = Mage::helper('weee')->getProductWeeeAttributes(
            $item->getProduct(),
            $address,
            $address->getQuote()->getBillingAddress(),
            $store->getWebsiteId()
        );

        $applied = array();
        $productTaxes = array();

        foreach ($attributes as $k=>$attribute) {
            $baseValue = $attribute->getAmount();
            $value = $store->convertPrice($baseValue);

            $rowValue = $value*$item->getQty();
            $baseRowValue = $baseValue*$item->getQty();

            $title = $attribute->getName();

            if ($item->getDiscountPercent() && Mage::helper('weee')->isDiscounted($store)) {
                $valueDiscount = $value/100*$item->getDiscountPercent();
                $baseValueDiscount = $baseValue/100*$item->getDiscountPercent();

                $rowValueDiscount = $rowValue/100*$item->getDiscountPercent();
                $baseRowValueDiscount = $baseRowValue/100*$item->getDiscountPercent();


//                $value        = $store->roundPrice($value-$valueDiscount);
//                $baseValue    = $store->roundPrice($baseValue-$baseValueDiscount);
//                $rowValue     = $store->roundPrice($rowValue-$rowValueDiscount);
//                $baseRowValue = $store->roundPrice($baseRowValue-$baseRowValueDiscount);


                $address->setDiscountAmount($address->getDiscountAmount()+$rowValueDiscount);
                $address->setBaseDiscountAmount($address->getBaseDiscountAmount()+$baseRowValueDiscount);
                
                $address->setGrandTotal($address->getGrandTotal() - $rowValueDiscount);
                $address->setBaseGrandTotal($address->getBaseGrandTotal() - $baseRowValueDiscount);
            }

            $oneDisposition = $baseOneDisposition = $disposition = $baseDisposition = 0;

            if (Mage::helper('weee')->isTaxable($store)) {
                $currentPercent = $item->getTaxPercent();
                $defaultPercent = $taxCalculationModel->getRate($defaultRateRequest->setProductClassId($item->getProduct()->getTaxClassId()));

                $valueBeforeVAT = $rowValue;
                $baseValueBeforeVAT = $baseRowValue;

                $oneDisposition = $store->roundPrice($value/(100+$defaultPercent)*$currentPercent);
                $baseOneDisposition = $store->roundPrice($baseValue/(100+$defaultPercent)*$currentPercent);

                $disposition = $store->roundPrice($rowValue/(100+$defaultPercent)*$currentPercent);
                $baseDisposition = $store->roundPrice($baseRowValue/(100+$defaultPercent)*$currentPercent);

                //$totalWeeeTax += $disposition;
                //$baseTotalWeeeTax += $baseDisposition;

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

                $item->setTaxBeforeDiscount($item->getTaxBeforeDiscount() + $disposition);
                $item->setBaseTaxBeforeDiscount($item->getBaseTaxBeforeDiscount() + $baseDisposition);

                $address->setTaxAmount($address->getTaxAmount() + $disposition);
                $address->setBaseTaxAmount($address->getBaseTaxAmount() + $baseDisposition);

                $rate = $taxCalculationModel->getRate($request->setProductClassId($item->getProduct()->getTaxClassId()));
                
                $this->_saveAppliedTaxes(
                   $address,
                   $taxCalculationModel->getAppliedRates($request),
                   $store->roundPrice($valueBeforeVAT-$rowValue),
                   $store->roundPrice($baseValueBeforeVAT-$baseRowValue),
                   $rate
                );

                $address->setGrandTotal($address->getGrandTotal() + $store->roundPrice($valueBeforeVAT-$rowValue));
                $address->setBaseGrandTotal($address->getBaseGrandTotal() + $store->roundPrice($baseValueBeforeVAT-$baseRowValue));
            }

            if (Mage::helper('weee')->includeInSubtotal($store)) {
                $address->setSubtotal($address->getSubtotal() + $rowValue);
                $address->setBaseSubtotal($address->getBaseSubtotal() + $baseRowValue);

                $address->setSubtotalWithDiscount($address->getSubtotalWithDiscount() + $rowValue);
                $address->setBaseSubtotalWithDiscount($address->getBaseSubtotalWithDiscount() + $baseRowValue);
            } else {
                $address->setTaxAmount($address->getTaxAmount() + $rowValue);
                $address->setBaseTaxAmount($address->getBaseTaxAmount() + $baseRowValue);
            }


            $productTaxes[] = array(
                'title'=>$title,
                'base_amount'=>$baseValue,
                'amount'=>$value,

                'row_amount'=>$rowValue,
                'base_row_amount'=>$baseRowValue,

                'base_amount_incl_tax'=>$baseValue+$baseOneDisposition,
                'amount_incl_tax'=>$value+$oneDisposition,

                'row_amount_incl_tax'=>$rowValue+$disposition,
                'base_row_amount_incl_tax'=>$baseRowValue+$baseDisposition,
            );

            $applied[] = array(
                'id'=>$attribute->getCode(),
                'percent'=>null,
                'hidden'=>Mage::helper('weee')->includeInSubtotal($store),
                'rates' => array(array(
                    'amount'=>$rowValue,
                    'base_amount'=>$baseRowValue,
                    'base_real_amount'=>$baseRowValue,
                    'code'=>$attribute->getCode(),
                    'title'=>$title,
                    'percent'=>null,
                    'position'=>1,
                    'priority'=>-1000+$k,
                ))
            );

            $item->setBaseWeeeTaxAppliedAmount($item->getBaseWeeeTaxAppliedAmount() + $baseValue);
            $item->setBaseWeeeTaxAppliedRowAmount($item->getBaseWeeeTaxAppliedRowAmount() + $baseRowValue);

            $item->setWeeeTaxAppliedAmount($item->getWeeeTaxAppliedAmount() + $value);
            $item->setWeeeTaxAppliedRowAmount($item->getWeeeTaxAppliedRowAmount() + $rowValue);
        }

        Mage::helper('weee')->setApplied($item, array_merge(Mage::helper('weee')->getApplied($item), $productTaxes));

        if ($updateParent) {
            $parent = $item->getParentItem();

            $parent->setBaseWeeeTaxDisposition($parent->getBaseWeeeTaxDisposition() + $item->getBaseWeeeTaxDisposition());
            $parent->setWeeeTaxDisposition($parent->getWeeeTaxDisposition() + $item->getWeeeTaxDisposition());

            $parent->setBaseWeeeTaxRowDisposition($parent->getBaseWeeeTaxRowDisposition() + $item->getBaseWeeeTaxRowDisposition());
            $parent->setWeeeTaxRowDisposition($parent->getWeeeTaxRowDisposition() + $item->getWeeeTaxRowDisposition());

            $parent->setBaseWeeeTaxAppliedAmount($parent->getBaseWeeeTaxAppliedAmount() + $item->getBaseWeeeTaxAppliedAmount());
            $parent->setBaseWeeeTaxAppliedRowAmount($parent->getBaseWeeeTaxAppliedRowAmount() + $item->getBaseWeeeTaxAppliedRowAmount());

            $parent->setWeeeTaxAppliedAmount($parent->getWeeeTaxAppliedAmount() + $item->getWeeeTaxAppliedAmount());
            $parent->setWeeeTaxAppliedRowAmount($parent->getWeeeTaxAppliedRowAmount() + $item->getWeeeTaxAppliedRowAmount());
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

    protected function _resetItemData($item)
    {
        Mage::helper('weee')->setApplied($item, array());

        $item->setBaseWeeeTaxDisposition(0);
        $item->setWeeeTaxDisposition(0);

        $item->setBaseWeeeTaxRowDisposition(0);
        $item->setWeeeTaxRowDisposition(0);

        $item->setBaseWeeeTaxAppliedAmount(0);
        $item->setBaseWeeeTaxAppliedRowAmount(0);

        $item->setWeeeTaxAppliedAmount(0);
        $item->setWeeeTaxAppliedRowAmount(0);
    }

    public function fetch(Mage_Sales_Model_Quote_Address $address)
    {
        return $this;
    }
}