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
 * @package     Mage_ConfigurableSwatches
 * @copyright  Copyright (c) 2006-2016 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Mage_ConfigurableSwatches_Helper_Data extends Mage_Core_Helper_Abstract
{
    const CONFIG_PATH_BASE = 'configswatches';
    const CONFIG_PATH_ENABLED = 'configswatches/general/enabled';
    const CONFIG_PATH_SWATCH_ATTRIBUTES = 'configswatches/general/swatch_attributes';
    const CONFIG_PATH_LIST_SWATCH_ATTRIBUTE = 'configswatches/general/product_list_attribute';


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
                (bool) Mage::getStoreConfig(self::CONFIG_PATH_ENABLED)
                && Mage::helper('configurableswatches/productlist')->getSwatchAttribute()
            );
        }
        return $this->_enabled;
    }

    /**
     * Return the formatted hyphenated string
     *
     * @param string $str
     * @return string
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
     * @param $key
     * @return string
     */
    public static function normalizeKey($key) {
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
            $this->_configAttributeIds = array();
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
     * @return string
     */
    public function getSwatchesProductJs()
    {
        /**
         * @var $product Mage_Catalog_Model_Product
         */
        $product = Mage::registry('current_product');
        if ($this->isEnabled() && $product) {
            $configAttrs = $this->getSwatchAttributeIds();
            $configurableAttributes = $product->getTypeInstance(true)->getConfigurableAttributesAsArray($product);
            foreach ($configurableAttributes as $configurableAttribute) {
                if (in_array($configurableAttribute['attribute_id'], $configAttrs)) {
                    return 'js/configurableswatches/swatches-product.js';
                }
            }
        }
        return '';
    }
}
