<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/**
 * Helper for fetching properties by product configurational item
 *
 * @package    Mage_Catalog
 */
class Mage_Catalog_Helper_Product_Configuration extends Mage_Core_Helper_Abstract implements Mage_Catalog_Helper_Product_Configuration_Interface
{
    public const XML_PATH_CONFIGURABLE_ALLOWED_TYPES = 'global/catalog/product/type/configurable/allow_product_types';

    protected $_moduleName = 'Mage_Catalog';

    /**
     * Retrieves product configuration options
     *
     * @return array
     */
    public function getCustomOptions(Mage_Catalog_Model_Product_Configuration_Item_Interface $item)
    {
        $product = $item->getProduct();
        $options = [];
        $optionIds = $item->getOptionByCode('option_ids');
        if ($optionIds) {
            $options = [];
            foreach (explode(',', $optionIds->getValue()) as $optionId) {
                $option = $product->getOptionById($optionId);
                if ($option) {
                    $itemOption = $item->getOptionByCode('option_' . $option->getId());
                    $group = $option->groupFactory($option->getType())
                        ->setOption($option)
                        ->setConfigurationItem($item)
                        ->setConfigurationItemOption($itemOption);

                    if ($option->getType() == 'file') {
                        $downloadParams = $item->getFileDownloadParams();
                        if ($downloadParams) {
                            $url = $downloadParams->getUrl();
                            if ($url) {
                                $group->setCustomOptionDownloadUrl($url);
                            }
                            $urlParams = $downloadParams->getUrlParams();
                            if ($urlParams) {
                                $group->setCustomOptionUrlParams($urlParams);
                            }
                        }
                    }

                    $options[] = [
                        'label' => $option->getTitle(),
                        'value' => $group->getFormattedOptionValue($itemOption->getValue()),
                        'print_value' => $group->getPrintableOptionValue($itemOption->getValue()),
                        'option_id' => $option->getId(),
                        'option_type' => $option->getType(),
                        'custom_view' => $group->isCustomizedView(),
                    ];
                }
            }
        }

        $addOptions = $item->getOptionByCode('additional_options');
        if ($addOptions) {
            $options = array_merge($options, unserialize($addOptions->getValue(), ['allowed_classes' => false]));
        }

        return $options;
    }

    /**
     * Retrieves configuration options for configurable product
     *
     * @return array
     */
    public function getConfigurableOptions(Mage_Catalog_Model_Product_Configuration_Item_Interface $item)
    {
        $product = $item->getProduct();
        $typeId = $product->getTypeId();
        if ($typeId != Mage_Catalog_Model_Product_Type_Configurable::TYPE_CODE) {
            Mage::throwException($this->__('Wrong product type to extract configurable options.'));
        }
        /** @var Mage_Catalog_Model_Product_Type_Configurable $productType */
        $productType = $product->getTypeInstance(true);
        $attributes = $productType->getSelectedAttributesInfo($product);
        return array_merge($attributes, $this->getCustomOptions($item));
    }

    /**
     * Retrieves configuration options for grouped product
     *
     * @return array
     */
    public function getGroupedOptions(Mage_Catalog_Model_Product_Configuration_Item_Interface $item)
    {
        $product = $item->getProduct();
        $typeId = $product->getTypeId();
        if ($typeId != Mage_Catalog_Model_Product_Type_Grouped::TYPE_CODE) {
            Mage::throwException($this->__('Wrong product type to extract configurable options.'));
        }

        $options = [];
        /** @var Mage_Catalog_Model_Product_Type_Grouped $typeInstance */
        $typeInstance = $product->getTypeInstance(true);
        $associatedProducts = $typeInstance->getAssociatedProducts($product);

        if ($associatedProducts) {
            foreach ($associatedProducts as $associatedProduct) {
                $qty = $item->getOptionByCode('associated_product_' . $associatedProduct->getId());
                $option = [
                    'label' => $associatedProduct->getName(),
                    'value' => ($qty && $qty->getValue()) ? $qty->getValue() : 0,
                ];

                $options[] = $option;
            }
        }

        $options = array_merge($options, $this->getCustomOptions($item));
        $isUnConfigured = true;
        foreach ($options as &$option) {
            if ($option['value']) {
                $isUnConfigured = false;
                break;
            }
        }
        return $isUnConfigured ? [] : $options;
    }

    /**
     * Retrieves product options list
     *
     * @return array
     */
    public function getOptions(Mage_Catalog_Model_Product_Configuration_Item_Interface $item)
    {
        $typeId = $item->getProduct()->getTypeId();
        return match ($typeId) {
            Mage_Catalog_Model_Product_Type_Configurable::TYPE_CODE => $this->getConfigurableOptions($item),
            Mage_Catalog_Model_Product_Type_Grouped::TYPE_CODE => $this->getGroupedOptions($item),
            default => $this->getCustomOptions($item),
        };
    }

    /**
     * Accept option value and return its formatted view
     *
     * @param mixed $optionValue
     * Method works well with these $optionValue format:
     *      1. String
     *      2. Indexed array e.g. array(val1, val2, ...)
     *      3. Associative array, containing additional option info, including option value, e.g.
     *          array
     *          (
     *              [label] => ...,
     *              [value] => ...,
     *              [print_value] => ...,
     *              [option_id] => ...,
     *              [option_type] => ...,
     *              [custom_view] =>...,
     *          )
     * @param array $params
     * All keys are options. Following supported:
     *  - 'maxLength': truncate option value if needed, default: do not truncate
     *  - 'cutReplacer': replacer for cut off value part when option value exceeds maxLength
     *
     * @return array
     */
    public function getFormattedOptionValue($optionValue, $params = null)
    {
        // Init params
        if (!$params) {
            $params = [];
        }
        $maxLength = $params['max_length'] ?? null;
        $cutReplacer = $params['cut_replacer'] ?? '...';

        // Proceed with option
        $optionInfo = [];

        // Define input data format
        if (is_array($optionValue)) {
            if (isset($optionValue['option_id'])) {
                $optionInfo = $optionValue;
                if (isset($optionInfo['value'])) {
                    $optionValue = $optionInfo['value'];
                }
            } elseif (isset($optionValue['value'])) {
                $optionValue = $optionValue['value'];
            }
        }

        // Render customized option view
        if (isset($optionInfo['custom_view']) && $optionInfo['custom_view']) {
            $_default = ['value' => $optionValue];
            if (isset($optionInfo['option_type'])) {
                try {
                    $group = Mage::getModel('catalog/product_option')->groupFactory($optionInfo['option_type']);
                    return ['value' => $group->getCustomizedView($optionInfo)];
                } catch (Exception $e) {
                    return $_default;
                }
            }
            return $_default;
        }

        // Truncate standard view
        $result = [];
        if (is_array($optionValue)) {
            $truncatedValue = implode("\n", $optionValue);
            $truncatedValue = nl2br($truncatedValue);
            return ['value' => $truncatedValue];
        } else {
            if ($maxLength) {
                $truncatedValue = Mage::helper('core/string')->truncate($optionValue, $maxLength, '');
            } else {
                $truncatedValue = $optionValue;
            }
            $truncatedValue = nl2br($truncatedValue);
        }

        $result = ['value' => $truncatedValue];

        if ($maxLength && (Mage::helper('core/string')->strlen($optionValue) > $maxLength)) {
            $result['value'] = $result['value'] . $cutReplacer;
            $optionValue = nl2br($optionValue);
            $result['full_view'] = $optionValue;
        }

        return $result;
    }

    /**
     * Get allowed product types for configurable product
     *
     * @return SimpleXMLElement
     */
    public function getConfigurableAllowedTypes()
    {
        return Mage::getConfig()
                ->getNode(self::XML_PATH_CONFIGURABLE_ALLOWED_TYPES)
                ->children();
    }
}
