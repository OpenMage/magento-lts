<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_ConfigurableSwatches
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2018-2025 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Mage
 * @package    Mage_ConfigurableSwatches
 */
class Mage_ConfigurableSwatches_Helper_Data extends Mage_Core_Helper_Abstract
{
    public const CONFIG_PATH_BASE = 'configswatches';
    public const CONFIG_PATH_ENABLED = 'configswatches/general/enabled';
    public const CONFIG_PATH_SWATCH_ATTRIBUTES = 'configswatches/general/swatch_attributes';
    public const CONFIG_PATH_LIST_SWATCH_ATTRIBUTE = 'configswatches/general/product_list_attribute';

    protected $_moduleName = 'Mage_ConfigurableSwatches';

    protected $_enabled = null;
    protected $_configAttributeIds = null;

    /**
     * Is the extension enabled?
     *
     * @return bool
     */
    public function isEnabled()
    {
        if (is_null($this->_enabled)) {
            $this->_enabled = (
                Mage::getStoreConfigFlag(self::CONFIG_PATH_ENABLED)
                && Mage::helper('configurableswatches/productlist')->getSwatchAttributeId()
            );
        }
        return $this->_enabled;
    }

    /**
     * Return the formatted hyphenated string
     *
     * @param string $str
     * @return string
     * @SuppressWarnings("PHPMD.ErrorControlOperator")
     */
    public function getHyphenatedString($str)
    {
        $result = false;
        if (function_exists('iconv')) {
            $result = @iconv('UTF-8', 'ASCII//TRANSLIT', $str); // will issue a notice on failure, we handle failure
        }

        if (!$result) {
            $result = dechex(crc32(self::normalizeKey($str)));
        }

        return preg_replace('/([^a-z0-9]+)/', '-', self::normalizeKey($result));
    }

    /**
     * Trims and lower-cases strings used as array indexes in json and for string matching in a
     * multi-byte compatible way if the mbstring module is available.
     *
     * @param string $key
     * @return string
     */
    public static function normalizeKey($key)
    {
        if ($key === null || $key === '') {
            return '';
        }
        if (function_exists('mb_strtolower')) {
            return trim(mb_strtolower($key, 'UTF-8'));
        }
        return trim(strtolower($key));
    }

    /**
     * Get list of attributes that should use swatches
     *
     * @return array
     */
    public function getSwatchAttributeIds()
    {
        if (is_null($this->_configAttributeIds)) {
            $this->_configAttributeIds = [];
            if (Mage::getStoreConfig(self::CONFIG_PATH_SWATCH_ATTRIBUTES)) {
                $this->_configAttributeIds = explode(',', Mage::getStoreConfig(self::CONFIG_PATH_SWATCH_ATTRIBUTES));
            }
        }
        return $this->_configAttributeIds;
    }

    /**
     * Determine if an attribute should be a swatch
     *
     * @param int|Mage_Eav_Model_Attribute $attr
     * @return bool
     */
    public function attrIsSwatchType($attr)
    {
        if ($attr instanceof Varien_Object) {
            $attr = $attr->getId();
        }
        $configAttrs = $this->getSwatchAttributeIds();
        return in_array($attr, $configAttrs);
    }

    /**
     * Get swatches product javascript
     *
     * @return string|null
     */
    public function getSwatchesProductJs()
    {
        /** @var Mage_Catalog_Model_Product $product */
        $product = Mage::registry('current_product');
        if ($this->isEnabled() && $product) {
            $configAttrs = $this->getSwatchAttributeIds();
            /** @var Mage_Catalog_Model_Product_Type_Configurable $productType */
            $productType = $product->getTypeInstance(true);
            $configurableAttributes = $productType->getConfigurableAttributesAsArray($product);
            foreach ($configurableAttributes as $configurableAttribute) {
                if (in_array($configurableAttribute['attribute_id'], $configAttrs)) {
                    return 'js/configurableswatches/swatches-product.js';
                }
            }
        }
        return null;
    }
}
