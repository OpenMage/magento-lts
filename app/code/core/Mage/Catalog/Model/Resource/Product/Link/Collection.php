<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/**
 * Catalog product links collection
 *
 * @package    Mage_Catalog
 */
class Mage_Catalog_Model_Resource_Product_Link_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Product object
     *
     * @var Mage_Catalog_Model_Product
     */
    protected $_product;

    /**
     * Product Link model class
     *
     * @var Mage_Catalog_Model_Product_Link
     */
    protected $_linkModel;

    /**
     * Product Link Type identifier
     *
     * @var Mage_Catalog_Model_Product_Type
     */
    protected $_linkTypeId;

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('catalog/product_link');
    }

    /**
     * Declare link model and initialize type attributes join
     *
     * @return $this
     */
    public function setLinkModel(Mage_Catalog_Model_Product_Link $linkModel)
    {
        $this->_linkModel = $linkModel;
        if ($linkModel->hasLinkTypeId()) {
            $this->_linkTypeId = $linkModel->getLinkTypeId();
        }

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
     * Add link's type to filter
     *
     * @return $this
     */
    public function addLinkTypeIdFilter()
    {
        if ($this->_linkTypeId) {
            $this->addFieldToFilter('link_type_id', ['eq' => $this->_linkTypeId]);
        }

        return $this;
    }

    /**
     * Add product to filter
     *
     * @return $this
     */
    public function addProductIdFilter()
    {
        if ($this->getProduct() && $this->getProduct()->getId()) {
            $this->addFieldToFilter('product_id', ['eq' => $this->getProduct()->getId()]);
        }

        return $this;
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
        $adapter = $this->getConnection();
        foreach ($attributes as $attribute) {
            $table = $this->getLinkModel()->getAttributeTypeTable($attribute['type']);
            $alias = sprintf('link_attribute_%s_%s', $attribute['code'], $attribute['type']);

            $aliasInCondition = $adapter->quoteColumnAs($alias, null);
            $this->getSelect()->joinLeft(
                [$alias => $table],
                $aliasInCondition . '.link_id = main_table.link_id AND '
                    . $aliasInCondition . '.product_link_attribute_id = ' . (int) $attribute['id'],
                [$attribute['code'] => 'value'],
            );
        }

        return $this;
    }
}
