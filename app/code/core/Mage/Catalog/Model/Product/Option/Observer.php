<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog Custom Options Observer
 *
 * @category   Mage
 * @package    Mage_Catalog
 */
class Mage_Catalog_Model_Product_Option_Observer
{
    /**
     * Copy quote custom option files to order custom option files
     *
     * @param Varien_Object $observer
     * @return $this
     */
    public function copyQuoteFilesToOrderFiles($observer)
    {
        /** @var Mage_Sales_Model_Quote_Item $quoteItem */
        $quoteItem = $observer->getEvent()->getItem();

        if (is_array($quoteItem->getOptions())) {
            foreach ($quoteItem->getOptions() as $itemOption) {
                $code = explode('_', $itemOption->getCode());
                if (isset($code[1]) && is_numeric($code[1]) && ($option = $quoteItem->getProduct()->getOptionById($code[1]))) {
                    if ($option->getType() == Mage_Catalog_Model_Product_Option::OPTION_TYPE_FILE) {
                        /** @var Mage_Catalog_Model_Product_Option $option */
                        try {
                            $group = $option->groupFactory($option->getType())
                                ->setQuoteItemOption($itemOption)
                                ->copyQuoteToOrder();
                        } catch (Exception $e) {
                            continue;
                        }
                    }
                }
            }
        }
        return $this;
    }
}
