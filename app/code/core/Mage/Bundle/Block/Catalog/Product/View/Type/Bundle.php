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
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog bundle product info block
 *
 * @category   Mage
 * @package    Mage_Bundle
 *
 * @method string getAddToCartUrl(Mage_Catalog_Model_Product $value)
 */
class Mage_Bundle_Block_Catalog_Product_View_Type_Bundle extends Mage_Catalog_Block_Product_View_Abstract
{
    /**
     * Renderers for bundle product options
     *
     * @var array
     */
    protected $_optionRenderers = [];

    /**
     * Bundle product options
     *
     * @var array
     */
    protected $_options         = null;

    /**
     * Default MAP renderer type
     *
     * @var string
     */
    protected $_mapRenderer = 'msrp_item';

    /**
     * Tier price template
     *
     * @var string
     */
    protected $_tierPriceDefaultTemplate  = 'bundle/catalog/product/view/option_tierprices.phtml';

    /**
     * Return an array of bundle product options
     *
     * @return array
     */
    public function getOptions()
    {
        if (!$this->_options) {
            $product = $this->getProduct();
            /** @var Mage_Bundle_Model_Product_Type $typeInstance */
            $typeInstance = $product->getTypeInstance(true);
            $typeInstance->setStoreFilter($product->getStoreId(), $product);

            $optionCollection = $typeInstance->getOptionsCollection($product);

            $selectionCollection = $typeInstance->getSelectionsCollection(
                $typeInstance->getOptionsIds($product),
                $product
            );

            $this->_options = $optionCollection->appendSelections(
                $selectionCollection,
                false,
                Mage::helper('catalog/product')->getSkipSaleableCheck()
            );
        }

        return $this->_options;
    }

    /**
     * Whether the bundle product has any option
     *
     * @return bool
     */
    public function hasOptions()
    {
        $this->getOptions();
        if (empty($this->_options) || !$this->getProduct()->isSalable()) {
            return false;
        }
        return true;
    }

    /**
     * Returns JSON encoded config to be used in JS scripts
     *
     * @return string
     */
    public function getJsonConfig()
    {
        Mage::app()->getLocale()->getJsPriceFormat();
        $optionsArray = $this->getOptions();
        $options      = [];
        $selected     = [];
        $currentProduct = $this->getProduct();
        /** @var Mage_Core_Helper_Data $coreHelper */
        $coreHelper   = Mage::helper('core');
        /** @var Mage_Bundle_Model_Product_Price $bundlePriceModel */
        $bundlePriceModel = Mage::getModel('bundle/product_price');

        $preConfiguredFlag = $currentProduct->hasPreconfiguredValues();
        if ($preConfiguredFlag) {
            $preConfiguredValues = $currentProduct->getPreconfiguredValues();
            $defaultValues       = [];
        }

        $position = 0;
        foreach ($optionsArray as $bundleOption) {
            /** @var Mage_Bundle_Model_Option $bundleOption */
            if (!$bundleOption->getSelections()) {
                continue;
            }

            $optionId = $bundleOption->getId();
            $option = [
                'selections' => [],
                'title'      => $bundleOption->getTitle(),
                'isMulti'    => in_array($bundleOption->getType(), ['multi', 'checkbox']),
                'position'   => $position++
            ];

            $selectionCount = count($bundleOption->getSelections());
            /** @var Mage_Tax_Helper_Data $taxHelper */
            $taxHelper = Mage::helper('tax');
            foreach ($bundleOption->getSelections() as $bundleSelection) {
                $selectionId = $bundleSelection->getSelectionId();
                $_qty = !($bundleSelection->getSelectionQty() * 1) ? '1' : $bundleSelection->getSelectionQty() * 1;
                // recalculate currency
                $tierPrices = $bundleSelection->getTierPrice();
                foreach ($tierPrices as &$tierPriceInfo) {
                    $tierPriceInfo['price'] =
                        $bundlePriceModel->getLowestPrice($currentProduct, $tierPriceInfo['price']);
                    $tierPriceInfo['website_price'] =
                        $bundlePriceModel->getLowestPrice($currentProduct, $tierPriceInfo['website_price']);
                    $tierPriceInfo['price'] = $coreHelper::currency($tierPriceInfo['price'], false, false);
                    $tierPriceInfo['priceInclTax'] = $taxHelper->getPrice(
                        $bundleSelection,
                        $tierPriceInfo['price'],
                        true,
                        null,
                        null,
                        null,
                        null,
                        null,
                        false
                    );
                    $tierPriceInfo['priceExclTax'] = $taxHelper->getPrice(
                        $bundleSelection,
                        $tierPriceInfo['price'],
                        false,
                        null,
                        null,
                        null,
                        null,
                        null,
                        false
                    );
                }
                unset($tierPriceInfo); // break the reference with the last element

                $itemPrice = $bundlePriceModel->getSelectionFinalTotalPrice(
                    $currentProduct,
                    $bundleSelection,
                    $currentProduct->getQty(),
                    $bundleSelection->getQty(),
                    false,
                    false
                );

                $canApplyMAP = false;

                /** @var Mage_Tax_Helper_Data $taxHelper */
                $taxHelper = Mage::helper('tax');

                $_priceInclTax = $taxHelper->getPrice(
                    $bundleSelection,
                    $itemPrice,
                    true,
                    null,
                    null,
                    null,
                    null,
                    null,
                    false
                );
                $_priceExclTax = $taxHelper->getPrice(
                    $bundleSelection,
                    $itemPrice,
                    false,
                    null,
                    null,
                    null,
                    null,
                    null,
                    false
                );

                if ($currentProduct->getPriceType() == Mage_Bundle_Model_Product_Price::PRICE_TYPE_FIXED) {
                    $_priceInclTax = $taxHelper->getPrice(
                        $currentProduct,
                        $itemPrice,
                        true,
                        null,
                        null,
                        null,
                        null,
                        null,
                        false
                    );
                    $_priceExclTax = $taxHelper->getPrice(
                        $currentProduct,
                        $itemPrice,
                        false,
                        null,
                        null,
                        null,
                        null,
                        null,
                        false
                    );
                }

                $selection = [
                    'qty'              => $_qty,
                    'customQty'        => $bundleSelection->getSelectionCanChangeQty(),
                    'price'            => $coreHelper::currency($bundleSelection->getFinalPrice(), false, false),
                    'priceInclTax'     => $coreHelper::currency($_priceInclTax, false, false),
                    'priceExclTax'     => $coreHelper::currency($_priceExclTax, false, false),
                    'priceValue'       => $coreHelper::currency($bundleSelection->getSelectionPriceValue(), false, false),
                    'priceType'        => $bundleSelection->getSelectionPriceType(),
                    'tierPrice'        => $tierPrices,
                    'name'             => $bundleSelection->getName(),
                    'plusDisposition'  => 0,
                    'minusDisposition' => 0,
                    'canApplyMAP'      => $canApplyMAP,
                    'tierPriceHtml'    => $this->getTierPriceHtml($bundleSelection, $currentProduct),
                ];

                $responseObject = new Varien_Object();
                $args = ['response_object' => $responseObject, 'selection' => $bundleSelection];
                Mage::dispatchEvent('bundle_product_view_config', $args);
                if (is_array($responseObject->getAdditionalOptions())) {
                    foreach ($responseObject->getAdditionalOptions() as $o => $v) {
                        $selection[$o] = $v;
                    }
                }
                $option['selections'][$selectionId] = $selection;

                if (($bundleSelection->getIsDefault() || ($selectionCount == 1 && $bundleOption->getRequired()))
                    && $bundleSelection->isSalable()
                ) {
                    $selected[$optionId][] = $selectionId;
                }
            }
            $options[$optionId] = $option;

            // Add attribute default value (if set)
            if ($preConfiguredFlag) {
                $configValue = $preConfiguredValues->getData('bundle_option/' . $optionId);
                if ($configValue) {
                    $defaultValues[$optionId] = $configValue;
                }
            }
        }

        $config = [
            'options'       => $options,
            'selected'      => $selected,
            'bundleId'      => $currentProduct->getId(),
            'priceFormat'   => Mage::app()->getLocale()->getJsPriceFormat(),
            'basePrice'     => $coreHelper::currency($currentProduct->getPrice(), false, false),
            'priceType'     => $currentProduct->getPriceType(),
            'specialPrice'  => $currentProduct->getSpecialPrice(),
            'includeTax'    => Mage::helper('tax')->priceIncludesTax() ? 'true' : 'false',
            'isFixedPrice'  => $this->getProduct()->getPriceType() == Mage_Bundle_Model_Product_Price::PRICE_TYPE_FIXED,
            'isMAPAppliedDirectly' => Mage::helper('catalog')->canApplyMsrp($this->getProduct(), null, false)
        ];

        if ($preConfiguredFlag && !empty($defaultValues)) {
            $config['defaultValues'] = $defaultValues;
        }

        return $coreHelper->jsonEncode($config);
    }

    /**
     * Add renderer for an option type, e.g., select, radio button, etc.
     *
     * @param string $type
     * @param string $block
     */
    public function addRenderer($type, $block)
    {
        $this->_optionRenderers[$type] = $block;
    }

    /**
     * Get option html
     *
     * @param Mage_Catalog_Model_Product_Option $option
     * @return string
     */
    public function getOptionHtml($option)
    {
        if (!isset($this->_optionRenderers[$option->getType()])) {
            return $this->__('There is no defined renderer for "%s" option type.', $option->getType());
        }
        return $this->getLayout()->createBlock($this->_optionRenderers[$option->getType()])
            ->setOption($option)->toHtml();
    }
}
