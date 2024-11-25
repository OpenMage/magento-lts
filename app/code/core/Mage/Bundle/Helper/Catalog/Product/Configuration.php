<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Bundle
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Helper for fetching properties by product configurational item
 *
 * @category   Mage
 * @package    Mage_Bundle
 */
class Mage_Bundle_Helper_Catalog_Product_Configuration extends Mage_Core_Helper_Abstract implements Mage_Catalog_Helper_Product_Configuration_Interface
{
    protected $_moduleName = 'Mage_Bundle';

    /**
     * Get selection quantity
     *
     * @param Mage_Catalog_Model_Product $product
     * @param int $selectionId
     *
     * @return float
     */
    public function getSelectionQty($product, $selectionId)
    {
        $selectionQty = $product->getCustomOption('selection_qty_' . $selectionId);
        if ($selectionQty) {
            return $selectionQty->getValue();
        }
        return 0;
    }

    /**
     * Obtain final price of selection in a bundle product
     *
     * @param Mage_Catalog_Model_Product $selectionProduct
     * @return float
     */
    public function getSelectionFinalPrice(
        Mage_Catalog_Model_Product_Configuration_Item_Interface $item,
        $selectionProduct
    ) {
        $selectionProduct->unsetData('final_price');
        return $item->getProduct()->getPriceModel()->getSelectionFinalTotalPrice(
            $item->getProduct(),
            $selectionProduct,
            $item->getQty() * 1,
            $this->getSelectionQty($item->getProduct(), $selectionProduct->getSelectionId()),
            false,
            true,
        );
    }

    /**
     * Get bundled selections (slections-products collection)
     *
     * Returns array of options objects.
     * Each option object will contain array of selections objects
     *
     * @return array
     */
    public function getBundleOptions(Mage_Catalog_Model_Product_Configuration_Item_Interface $item)
    {
        $options = [];
        $product = $item->getProduct();

        /** @var Mage_Bundle_Model_Product_Type $typeInstance */
        $typeInstance = $product->getTypeInstance(true);

        // get bundle options
        $optionsQuoteItemOption = $item->getOptionByCode('bundle_option_ids');
        $bundleOptionsIds = $optionsQuoteItemOption ? unserialize($optionsQuoteItemOption->getValue(), ['allowed_classes' => false]) : [];
        if ($bundleOptionsIds) {
            $optionsCollection = $typeInstance->getOptionsByIds($bundleOptionsIds, $product);

            // get and add bundle selections collection
            $selectionsQuoteItemOption = $item->getOptionByCode('bundle_selection_ids');

            $bundleSelectionIds = unserialize($selectionsQuoteItemOption->getValue(), ['allowed_classes' => false]);

            if (!empty($bundleSelectionIds)) {
                $selectionsCollection = $typeInstance->getSelectionsByIds(
                    unserialize($selectionsQuoteItemOption->getValue(), ['allowed_classes' => false]),
                    $product,
                );

                /** @var Mage_Bundle_Model_Option[] $bundleOptions */
                $bundleOptions = $optionsCollection->appendSelections($selectionsCollection, true);
                foreach ($bundleOptions as $bundleOption) {
                    if ($bundleOption->getSelections()) {
                        $option = [
                            'label' => $bundleOption->getTitle(),
                            'value' => [],
                        ];

                        $bundleSelections = $bundleOption->getSelections();

                        foreach ($bundleSelections as $bundleSelection) {
                            $qty = $this->getSelectionQty($product, $bundleSelection->getSelectionId()) * 1;
                            if ($qty) {
                                $option['value'][] = $qty . ' x ' . $this->escapeHtml($bundleSelection->getName())
                                    . ' ' . Mage::helper('core')->currency(
                                        $this->getSelectionFinalPrice($item, $bundleSelection),
                                    );
                            }
                        }

                        if ($option['value']) {
                            $options[] = $option;
                        }
                    }
                }
            }
        }

        return $options;
    }

    /**
     * Retrieves product options list
     *
     * @return array
     */
    public function getOptions(Mage_Catalog_Model_Product_Configuration_Item_Interface $item)
    {
        return array_merge(
            $this->getBundleOptions($item),
            Mage::helper('catalog/product_configuration')->getCustomOptions($item),
        );
    }
}
