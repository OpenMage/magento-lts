<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 * @category   Mage
 * @package    Mage_Catalog
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
