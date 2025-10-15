<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_SalesRule
 */

/**
 * @package    Mage_SalesRule
 */
class Mage_SalesRule_Model_Rule_Condition_Product_Combine extends Mage_Rule_Model_Condition_Combine
{
    /**
     * Quote item conditions group
     */
    public const PRODUCT_ATTRIBUTES_TYPE_QUOTE_ITEM = 'quote_item';

    /**
     * "Product attribute match a value" conditions group
     */
    public const PRODUCT_ATTRIBUTES_TYPE_PRODUCT = 'product_attribute_match';

    /**
     * "Product attribute is set" conditions group
     */
    public const PRODUCT_ATTRIBUTES_TYPE_ISSET = 'product_attribute_isset';

    /**
     * Products attributes info
     * @var array|null
     */
    protected $_productAttributesInfo = null;

    /**
     * Initialize and retrieve a helper instance
     * @return Mage_Catalog_Helper_Data
     */
    protected function _getHelper()
    {
        return Mage::helper('catalog');
    }

    /**
     * Check whether the attribute is a quote item attribute
     * @param string $attributeCode
     * @return bool
     */
    protected function _getIsQuoteItemAttribute($attributeCode)
    {
        return str_starts_with($attributeCode, 'quote_item_');
    }

    /**
     * Add an attribute condition to the conditions group
     * @param string $conditionType
     * @param string $conditionModel
     * @param string $attributeCode
     * @param string $attributeLabel
     *
     * @return $this
     */
    protected function _addAttributeToConditionGroup($conditionType, $conditionModel, $attributeCode, $attributeLabel)
    {
        if (!array_key_exists($conditionType, $this->_productAttributesInfo)) {
            $this->_productAttributesInfo[$conditionType] = [];
        }

        $conditionKey = sprintf('%s|%s', $conditionModel, $attributeCode);

        $this->_productAttributesInfo[$conditionType][$conditionKey] = [
            'label' => $attributeLabel,
            'value' => $conditionKey,
        ];

        return $this;
    }

    /**
     * Retrieve a conditions by group_id
     * @param string $conditionsGroup
     *
     * @return array
     */
    protected function _getAttributeConditions($conditionsGroup)
    {
        $this->_initializeProductAttributesInfo();
        return array_key_exists($conditionsGroup, $this->_productAttributesInfo)
            ? $this->_productAttributesInfo[$conditionsGroup]
            : [];
    }

    /**
     * CHeck whether the product attribute information exists and initialize it if missing
     * @return $this
     */
    protected function _initializeProductAttributesInfo()
    {
        if (is_null($this->_productAttributesInfo)) {
            $this->_productAttributesInfo = [];
            $productAttributes = Mage::getModel('salesrule/rule_condition_product')
                ->loadAttributeOptions()
                ->getAttributeOption();
            foreach ($productAttributes as $attributeCode => $attributeLabel) {
                if ($this->_getIsQuoteItemAttribute($attributeCode)) {
                    $this->_addAttributeToConditionGroup(
                        self::PRODUCT_ATTRIBUTES_TYPE_QUOTE_ITEM,
                        'salesrule/rule_condition_product',
                        $attributeCode,
                        $attributeLabel,
                    );
                } else {
                    $this->_addAttributeToConditionGroup(
                        self::PRODUCT_ATTRIBUTES_TYPE_PRODUCT,
                        'salesrule/rule_condition_product',
                        $attributeCode,
                        $attributeLabel,
                    )->_addAttributeToConditionGroup(
                        self::PRODUCT_ATTRIBUTES_TYPE_ISSET,
                        'salesrule/rule_condition_product_attribute_assigned',
                        $attributeCode,
                        $attributeLabel,
                    );
                }
            }
        }

        return $this;
    }

    /**
     * Initialize a rule condition
     */
    public function __construct()
    {
        parent::__construct();
        $this->setType('salesrule/rule_condition_product_combine');
    }

    /**
     * Generate a conditions data
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        $conditions = parent::getNewChildSelectOptions();
        return array_merge_recursive(
            $conditions,
            [
                [
                    'label' => Mage::helper('catalog')->__('Conditions Combination'),
                    'value' => 'salesrule/rule_condition_product_combine',
                ],
                [
                    'label' => Mage::helper('catalog')->__('Cart Item Attribute'),
                    'value' => $this->_getAttributeConditions(self::PRODUCT_ATTRIBUTES_TYPE_QUOTE_ITEM),
                ],
                [
                    'label' => Mage::helper('catalog')->__('Product Attribute'),
                    'value' => $this->_getAttributeConditions(self::PRODUCT_ATTRIBUTES_TYPE_PRODUCT),
                ],
                [
                    'label' => $this->_getHelper()->__('Product Attribute Assigned'),
                    'value' => $this->_getAttributeConditions(self::PRODUCT_ATTRIBUTES_TYPE_ISSET),
                ],
            ],
        );
    }

    /**
     * Collect all validated attributes
     * @param Mage_Catalog_Model_Resource_Product_Collection $productCollection
     * @return $this
     */
    public function collectValidatedAttributes($productCollection)
    {
        foreach ($this->getConditions() as $condition) {
            $condition->collectValidatedAttributes($productCollection);
        }

        return $this;
    }

    /**
     * Validate a condition with the checking of the child value
     *
     * @return bool
     */
    public function validate(Varien_Object $object)
    {
        /** @var Mage_Catalog_Model_Product $product */
        $product = $object->getProduct();
        if (!($product instanceof Mage_Catalog_Model_Product)) {
            $product = Mage::getModel('catalog/product')->load($object->getProductId());
        }

        $valid = parent::validate($object);
        if (!$valid && $product->getTypeId() == Mage_Catalog_Model_Product_Type_Configurable::TYPE_CODE) {
            $children = $object->getChildren();
            if (is_array($children) && isset($children[0])) {
                $child = $children[0];

                /** @var Mage_Catalog_Model_Product $childProduct */
                $childProduct = Mage::getModel('catalog/product')->load($child->getProductId());
                $childProduct
                    ->setQuoteItemQty($object->getQty())
                    ->setQuoteItemPrice($object->getPrice())
                    ->setQuoteItemRowTotal($object->getBaseRowTotal());

                if (!$childProduct->isVisibleInSiteVisibility()) {
                    $childProduct->setCategoryIds($product->getCategoryIds());
                }

                $valid = parent::validate($childProduct);
            }
        }

        return $valid;
    }
}
