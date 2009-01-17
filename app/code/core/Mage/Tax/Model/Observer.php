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
 * @package    Mage_Tax
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Tax Event Observer
 *
 * @author Magento Core Team <core@magentocommerce.com>
 */
class Mage_Tax_Model_Observer
{
    /**
     * Put quote address tax information into order
     *
     * @param Varien_Event_Observer $observer
     */
    public function salesEventConvertQuoteAddressToOrder(Varien_Event_Observer $observer)
    {
        $address = $observer->getEvent()->getAddress();
        $order = $observer->getEvent()->getOrder();

        $taxes = $address->getAppliedTaxes();
        if (is_array($taxes)) {
            if (is_array($order->getAppliedTaxes())) {
                $taxes = array_merge($order->getAppliedTaxes(), $taxes);
            }
            $order->setAppliedTaxes($taxes);
            $order->setConvertingFromQuote(true);
        }
    }

    /**
     * Save order tax information
     *
     * @param Varien_Event_Observer $observer
     */
    public function salesEventOrderAfterSave(Varien_Event_Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        if (!$order->getConvertingFromQuote()) {
            return;
        }

        $taxes = $order->getAppliedTaxes();
        foreach ($taxes as $row) {
            foreach ($row['rates'] as $tax) {
                if (is_null($row['percent'])) {
                    $baseRealAmount = $row['base_amount'];
                } else {
                    if ($row['percent'] == 0 || $tax['percent'] == 0) {
                        continue;
                    }
                    $baseRealAmount = $row['base_amount']/$row['percent']*$tax['percent'];
                }
                $hidden = (isset($row['hidden']) ? $row['hidden'] : 0);
                $data = array(
                            'order_id'=>$order->getId(),
                            'code'=>$tax['code'],
                            'title'=>$tax['title'],
                            'hidden'=>$hidden,
                            'percent'=>$tax['percent'],
                            'priority'=>$tax['priority'],
                            'position'=>$tax['position'],
                            'amount'=>$row['amount'],
                            'base_amount'=>$row['base_amount'],
                            'process'=>$row['process'],
                            'base_real_amount'=>$baseRealAmount,
                            );

                Mage::getModel('sales/order_tax')->setData($data)->save();
            }
        }
    }

    /**
     * Prepare select which is using to select index data for layered navigation
     *
     * @param   Varien_Event_Observer $observer
     * @return  Mage_Tax_Model_Observer
     */
    public function prepareCatalogIndexPriceSelect(Varien_Event_Observer $observer)
    {
        $table = $observer->getEvent()->getTable();
        $response = $observer->getEvent()->getResponseObject();
        $select = $observer->getEvent()->getSelect();
        $storeId = $observer->getEvent()->getStoreId();

        $additionalCalculations = $response->getAdditionalCalculations();
        $calculation = Mage::helper('tax')->getPriceTaxSql(
            $table . '.value', 'IFNULL(tax_class_c.value, tax_class_d.value)'
        );

        if (!empty($calculation)) {
            $additionalCalculations[] = $calculation;
            $response->setAdditionalCalculations($additionalCalculations);
            Mage::helper('tax')->joinTaxClass($select, $storeId, $table);
        }

        return $this;
    }
}
