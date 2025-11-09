<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/**
 * Product type model
 *
 * @package    Mage_Catalog
 */
class Mage_Catalog_Model_Product_Type
{
    /**
     * Available product types
     */
    public const TYPE_SIMPLE       = 'simple';

    public const TYPE_BUNDLE       = 'bundle';

    public const TYPE_CONFIGURABLE = 'configurable';

    public const TYPE_GROUPED      = 'grouped';

    public const TYPE_VIRTUAL      = 'virtual';

    public const DEFAULT_TYPE      = 'simple';

    public const DEFAULT_TYPE_MODEL    = 'catalog/product_type_simple';

    public const DEFAULT_PRICE_MODEL   = 'catalog/product_type_price';

    protected static $_types;

    protected static $_compositeTypes;

    protected static $_priceModels;

    protected static $_typesPriority;

    /**
     * Product type instance factory
     *
     * @param Mage_Catalog_Model_Product|Varien_Object $product
     * @param bool $singleton
     * @return false|Mage_Core_Model_Abstract
     */
    public static function factory($product, $singleton = false)
    {
        $types = self::getTypes();
        $typeId = $product->getTypeId();

        if (!empty($types[$typeId]['model'])) {
            $typeModelName = $types[$typeId]['model'];
        } else {
            $typeModelName = self::DEFAULT_TYPE_MODEL;
            $typeId = self::DEFAULT_TYPE;
        }

        if ($singleton === true) {
            $typeModel = Mage::getSingleton($typeModelName);
        } else {
            $typeModel = Mage::getModel($typeModelName);
            $typeModel->setProduct($product);
        }

        $typeModel->setConfig($types[$typeId]);
        return $typeModel;
    }

    /**
     * Product type price model factory
     *
     * @param   string $productType
     * @return  Mage_Catalog_Model_Product_Type_Price|Mage_Core_Model_Abstract
     */
    public static function priceFactory($productType)
    {
        if (isset(self::$_priceModels[$productType])) {
            return self::$_priceModels[$productType];
        }

        $types = self::getTypes();

        if (!empty($types[$productType]['price_model'])) {
            $priceModelName = $types[$productType]['price_model'];
        } else {
            $priceModelName = self::DEFAULT_PRICE_MODEL;
        }

        self::$_priceModels[$productType] = Mage::getModel($priceModelName);
        return self::$_priceModels[$productType];
    }

    /**
     * @return array
     */
    public static function getOptionArray()
    {
        $options = [];
        foreach (self::getTypes() as $typeId => $type) {
            $options[$typeId] = Mage::helper('catalog')->__($type['label']);
        }

        return $options;
    }

    /**
     * Retrieve option array
     *
     * @return array
     */
    public static function toOptionArray()
    {
        return self::getOptionArray();
    }

    /**
     * @return array
     */
    public static function getAllOption()
    {
        $options = self::getOptionArray();
        array_unshift($options, ['value' => '', 'label' => '']);
        return $options;
    }

    /**
     * @return array
     */
    public static function getAllOptions()
    {
        $res = [];
        $res[] = ['value' => '', 'label' => ''];
        foreach (self::getOptionArray() as $index => $value) {
            $res[] = [
                'value' => $index,
                'label' => $value,
            ];
        }

        return $res;
    }

    /**
     * @return array
     */
    public static function getOptions()
    {
        $res = [];
        foreach (self::getOptionArray() as $index => $value) {
            $res[] = [
                'value' => $index,
                'label' => $value,
            ];
        }

        return $res;
    }

    /**
     * @param string $optionId
     * @return null|mixed
     */
    public static function getOptionText($optionId)
    {
        $options = self::getOptionArray();
        return $options[$optionId] ?? null;
    }

    /**
     * @return array|string
     */
    public static function getTypes()
    {
        if (is_null(self::$_types)) {
            $productTypes = Mage::getConfig()->getNode('global/catalog/product/type')->asArray();
            foreach ($productTypes as $productKey => $productConfig) {
                $moduleName = $productConfig['@']['module'] ?? 'catalog';
                $translatedLabel = Mage::helper($moduleName)->__($productConfig['label']);
                $productTypes[$productKey]['label'] = $translatedLabel;
            }

            self::$_types = $productTypes;
        }

        return self::$_types;
    }

    /**
     * Return composite product type Ids
     *
     * @return array
     */
    public static function getCompositeTypes()
    {
        if (is_null(self::$_compositeTypes)) {
            self::$_compositeTypes = [];
            $types = self::getTypes();
            foreach ($types as $typeId => $typeInfo) {
                if (array_key_exists('composite', $typeInfo) && $typeInfo['composite']) {
                    self::$_compositeTypes[] = $typeId;
                }
            }
        }

        return self::$_compositeTypes;
    }

    /**
     * Return product types by type indexing priority
     *
     * @return array
     */
    public static function getTypesByPriority()
    {
        if (is_null(self::$_typesPriority)) {
            self::$_typesPriority = [];
            $a = [];
            $b = [];

            $types = self::getTypes();
            foreach ($types as $typeId => $typeInfo) {
                $priority = isset($typeInfo['index_priority']) ? abs((int) $typeInfo['index_priority']) : 0;
                if (!empty($typeInfo['composite'])) {
                    $b[$typeId] = $priority;
                } else {
                    $a[$typeId] = $priority;
                }
            }

            asort($a, SORT_NUMERIC);
            asort($b, SORT_NUMERIC);

            foreach (array_keys($a) as $typeId) {
                self::$_typesPriority[$typeId] = $types[$typeId];
            }

            foreach (array_keys($b) as $typeId) {
                self::$_typesPriority[$typeId] = $types[$typeId];
            }
        }

        return self::$_typesPriority;
    }
}
