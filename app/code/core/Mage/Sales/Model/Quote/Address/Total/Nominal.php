<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Sales
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Nominal items total
 * Collects only items segregated by isNominal property
 * Aggregates row totals per item
 *
 * @category   Mage
 * @package    Mage_Checkout
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Quote_Address_Total_Nominal extends Mage_Sales_Model_Quote_Address_Total_Abstract
{
    /**
     * Invoke collector for nominal items
     *
     * @param Mage_Sales_Model_Quote_Address $address
     * @return Mage_Sales_Model_Quote_Address_Total_Nominal
     */
    public function collect(Mage_Sales_Model_Quote_Address $address)
    {
        $collector = Mage::getSingleton(
            'sales/quote_address_total_nominal_collector',
            ['store' => $address->getQuote()->getStore()]
        );

        // invoke nominal totals
        foreach ($collector->getCollectors() as $model) {
            $model->collect($address);
        }

        // aggregate collected amounts into one to have sort of grand total per item
        foreach ($address->getAllNominalItems() as $item) {
            $rowTotal = 0;
            $baseRowTotal = 0;
            $totalDetails = [];
            foreach ($collector->getCollectors() as $model) {
                $itemRowTotal = $model->getItemRowTotal($item);
                if ($model->getIsItemRowTotalCompoundable($item)) {
                    $rowTotal += $itemRowTotal;
                    $baseRowTotal += $model->getItemBaseRowTotal($item);
                    $isCompounded = true;
                } else {
                    $isCompounded = false;
                }
                if ((float)$itemRowTotal > 0 && $label = $model->getLabel()) {
                    $totalDetails[] = new Varien_Object([
                        'label'  => $label,
                        'amount' => $itemRowTotal,
                        'is_compounded' => $isCompounded,
                    ]);
                }
            }
            $item->setNominalRowTotal($rowTotal);
            $item->setBaseNominalRowTotal($baseRowTotal);
            $item->setNominalTotalDetails($totalDetails);
        }

        return $this;
    }

    /**
     * Fetch collected nominal items
     *
     * @param Mage_Sales_Model_Quote_Address $address
     * @return $this
     */
    public function fetch(Mage_Sales_Model_Quote_Address $address)
    {
        $items = $address->getAllNominalItems();
        if ($items) {
            $address->addTotal([
                'code'    => $this->getCode(),
                'title'   => Mage::helper('sales')->__('Nominal Items'),
                'items'   => $items,
                'area'    => 'footer',
            ]);
        }
        return $this;
    }
}
