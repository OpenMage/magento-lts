<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/**
 * Catalog product linked products collection
 *
 * @package    Mage_Catalog
 */
class Mage_Catalog_Model_Resource_Product_Link_Product_Collection extends Mage_Catalog_Model_Resource_Product_Collection
{
    /**
     * Store product model
     *
     * @var Mage_Catalog_Model_Product
     */
    protected $_product;

    /**
     * Store product link model
     *
     * @var Mage_Catalog_Model_Product_Link
     */
    protected $_linkModel;

    /**
     * Store link type id
     *
     * @var int
     */
    protected $_linkTypeId;

    /**
     * Store strong mode flag that determine if needed for inner join or left join of linked products
     *
     * @var bool
     */
    protected $_isStrongMode;

    /**
     * Store flag that determine if product filter was enabled
     *
     * @var bool
     */
    protected $_hasLinkFilter  = false;

    /**
     * Declare link model and initialize type attributes join
     *
     * @return $this
     */
    public function setLinkModel(Mage_Catalog_Model_Product_Link $linkModel)
    {
        $this->_linkModel = $linkModel;
        if ($linkModel->getLinkTypeId()) {
            $this->_linkTypeId = $linkModel->getLinkTypeId();
        }
        return $this;
    }

    /**
     * Enable strong mode for inner join of linked products
     *
     * @return $this
     */
    public function setIsStrongMode()
    {
        $this->_isStrongMode = true;
        return $this;
    }

    /**
     * Retrieve collection link model
     *
     * @return Mage_Catalog_Model_Product_Link
     */
    public function getLinkModel()
    {
        return $this->_linkModel;
    }

    /**
     * Initialize collection parent product and add limitation join
     *
     * @return $this
     */
    public function setProduct(Mage_Catalog_Model_Product $product)
    {
        $this->_product = $product;
        if ($product && $product->getId()) {
            $this->_hasLinkFilter = true;
            $this->setStore($product->getStore());
        }
        return $this;
    }

    /**
     * Retrieve collection base product object
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        return $this->_product;
    }

    /**
     * Exclude products from filter
     *
     * @param array $products
     * @return $this
     */
    public function addExcludeProductFilter($products)
    {
        if (!empty($products)) {
            if (!is_array($products)) {
                $products = [$products];
            }
            $this->_hasLinkFilter = true;
            $this->getSelect()->where('links.linked_product_id NOT IN (?)', $products);
        }
        return $this;
    }

    /**
     * Add products to filter
     *
     * @param array|int|string $products
     * @return $this
     */
    public function addProductFilter($products)
    {
        if (!empty($products)) {
            if (!is_array($products)) {
                $products = [$products];
            }
            $this->getSelect()->where('links.product_id IN (?)', $products);
            $this->_hasLinkFilter = true;
        }

        return $this;
    }

    /**
     * Add random sorting order
     *
     * @return $this
     */
    public function setRandomOrder()
    {
        $this->getSelect()->orderRand('main_table.entity_id');
        return $this;
    }

    /**
     * Setting group by to exclude duplications in collection
     *
     * @param string $groupBy
     * @return $this
     */
    public function setGroupBy($groupBy = 'e.entity_id')
    {
        $this->getSelect()->group($groupBy);

        /*
         * Allow Analytic functions usage
         */
        $this->_useAnalyticFunction = true;

        return $this;
    }

    /**
     * Join linked products when specified link model
     *
     * @inheritDoc
     */
    protected function _beforeLoad()
    {
        if ($this->getLinkModel()) {
            $this->_joinLinks();
        }
        return parent::_beforeLoad();
    }

    /**
     * Join linked products and their attributes
     *
     * @return $this
     */
    protected function _joinLinks()
    {
        $select  = $this->getSelect();
        $adapter = $select->getAdapter();

        $joinCondition = [
            'links.linked_product_id = e.entity_id',
            $adapter->quoteInto('links.link_type_id = ?', $this->_linkTypeId),
        ];
        $joinType = 'join';
        if ($this->getProduct() && $this->getProduct()->getId()) {
            $productId = $this->getProduct()->getId();
            if ($this->_isStrongMode) {
                $this->getSelect()->where('links.product_id = ?', (int) $productId);
            } else {
                $joinType = 'joinLeft';
                $joinCondition[] = $adapter->quoteInto('links.product_id = ?', $productId);
            }
            $this->addFieldToFilter('entity_id', ['neq' => $productId]);
        } elseif ($this->_isStrongMode) {
            $this->addFieldToFilter('entity_id', ['eq' => -1]);
        }
        if ($this->_hasLinkFilter) {
            $select->$joinType(
                ['links' => $this->getTable('catalog/product_link')],
                implode(' AND ', $joinCondition),
                ['link_id'],
            );
            $this->joinAttributes();
        }
        return $this;
    }

    /**
     * Enable sorting products by its position
     *
     * @param string $dir sort type asc|desc
     * @return $this
     */
    public function setPositionOrder($dir = self::SORT_ORDER_ASC)
    {
        if ($this->_hasLinkFilter) {
            $this->getSelect()->order('position ' . $dir);
        }
        return $this;
    }

    /**
     * Enable sorting products by its attribute set name
     *
     * @param string $dir sort type asc|desc
     * @return $this
     */
    public function setAttributeSetIdOrder($dir = self::SORT_ORDER_ASC)
    {
        $this->getSelect()
            ->joinLeft(
                ['set' => $this->getTable('eav/attribute_set')],
                'e.attribute_set_id = set.attribute_set_id',
                ['attribute_set_name'],
            )
            ->order('set.attribute_set_name ' . $dir);
        return $this;
    }

    /**
     * Get table alias for link model attribute
     *
     * @param string $attributeCode
     * @param string $attributeType
     *
     * @return string
     */
    protected function _getLinkAttributeTableAlias($attributeCode, $attributeType)
    {
        return sprintf('link_attribute_%s_%s', $attributeCode, $attributeType);
    }

    /**
     * Join attributes
     *
     * @return $this
     */
    public function joinAttributes()
    {
        if (!$this->getLinkModel()) {
            return $this;
        }
        $attributes = $this->getLinkModel()->getAttributes();

        foreach ($attributes as $attribute) {
            $table = $this->getLinkModel()->getAttributeTypeTable($attribute['type']);
            $alias = $this->_getLinkAttributeTableAlias($attribute['code'], $attribute['type']);

            $joinCondiotion = [
                "{$alias}.link_id = links.link_id",
                $this->getSelect()->getAdapter()->quoteInto("{$alias}.product_link_attribute_id = ?", $attribute['id']),
            ];
            $this->getSelect()->joinLeft(
                [$alias => $table],
                implode(' AND ', $joinCondiotion),
                [$attribute['code'] => 'value'],
            );
        }

        return $this;
    }

    /**
     * Set sorting order
     *
     * $attribute can also be an array of attributes
     *
     * @inheritDoc
     */
    public function setOrder($attribute, $dir = self::SORT_ORDER_ASC)
    {
        if ($attribute == 'position') {
            return $this->setPositionOrder($dir);
        } elseif ($attribute == 'attribute_set_id') {
            return $this->setAttributeSetIdOrder($dir);
        }
        return parent::setOrder($attribute, $dir);
    }

    /**
     * Add specific link model attribute to collection filter
     *
     * @param string $attributeCode
     * @param array|null $condition
     *
     * @return $this
     */
    public function addLinkModelFieldToFilter($attributeCode, $condition = null)
    {
        if (!$this->getProduct() || !$this->getProduct()->getId()) {
            return $this;
        }

        $attribute = null;
        foreach ($this->getLinkModel()->getAttributes() as $attributeData) {
            if ($attributeData['code'] == $attributeCode) {
                $attribute = $attributeData;
                break;
            }
        }

        if (!$attribute) {
            return $this;
        }

        $this->_hasLinkFilter = true;

        $field = $this->_getLinkAttributeTableAlias($attribute['code'], $attribute['type']) . '.value';
        $this->getSelect()->where($this->_getConditionSql($field, $condition));

        return $this;
    }
}
