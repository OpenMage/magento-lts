<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/**
 * Catalog product link model
 *
 * @package    Mage_Catalog
 *
 * @method Mage_Catalog_Model_Resource_Product_Link _getResource()
 * @method Mage_Catalog_Model_Resource_Product_Link getResource()
 * @method int getProductId()
 * @method $this setProductId(int $value)
 * @method int getLinkedProductId()
 * @method $this setLinkedProductId(int $value)
 * @method int getLinkTypeId()
 * @method $this setLinkTypeId(int $value)
 */
class Mage_Catalog_Model_Product_Link extends Mage_Core_Model_Abstract
{
    public const LINK_TYPE_RELATED     = 1;

    public const LINK_TYPE_GROUPED     = 3;

    public const LINK_TYPE_UPSELL      = 4;

    public const LINK_TYPE_CROSSSELL   = 5;

    protected $_attributeCollection = null;

    /**
     * Initialize resource
     */
    protected function _construct()
    {
        $this->_init('catalog/product_link');
    }

    /**
     * @return $this
     */
    public function useRelatedLinks()
    {
        $this->setLinkTypeId(self::LINK_TYPE_RELATED);
        return $this;
    }

    /**
     * @return $this
     */
    public function useGroupedLinks()
    {
        $this->setLinkTypeId(self::LINK_TYPE_GROUPED);
        return $this;
    }

    /**
     * @return $this
     */
    public function useUpSellLinks()
    {
        $this->setLinkTypeId(self::LINK_TYPE_UPSELL);
        return $this;
    }

    /**
     * @return $this
     */
    public function useCrossSellLinks()
    {
        $this->setLinkTypeId(self::LINK_TYPE_CROSSSELL);
        return $this;
    }

    /**
     * Retrieve table name for attribute type
     *
     * @param   string $type
     * @return  string
     */
    public function getAttributeTypeTable($type)
    {
        return $this->_getResource()->getAttributeTypeTable($type);
    }

    /**
     * Retrieve linked product collection
     * @return Mage_Catalog_Model_Resource_Product_Link_Product_Collection
     */
    public function getProductCollection()
    {
        return Mage::getResourceModel('catalog/product_link_product_collection')
            ->setLinkModel($this);
    }

    /**
     * Retrieve link collection
     * @return Mage_Catalog_Model_Resource_Product_Link_Collection
     */
    public function getLinkCollection()
    {
        return Mage::getResourceModel('catalog/product_link_collection')
            ->setLinkModel($this);
    }

    /**
     * @param int|null $type
     * @return array
     */
    public function getAttributes($type = null)
    {
        if (is_null($type)) {
            $type = $this->getLinkTypeId();
        }

        return $this->_getResource()->getAttributesByType($type);
    }

    /**
     * Save data for product relations
     *
     * @param   Mage_Catalog_Model_Product $product
     * @return  Mage_Catalog_Model_Product_Link
     */
    public function saveProductRelations($product)
    {
        $data = $product->getRelatedLinkData();
        if (!is_null($data)) {
            $this->_getResource()->saveProductLinks($product, $data, self::LINK_TYPE_RELATED);
        }

        $data = $product->getUpSellLinkData();
        if (!is_null($data)) {
            $this->_getResource()->saveProductLinks($product, $data, self::LINK_TYPE_UPSELL);
        }

        $data = $product->getCrossSellLinkData();
        if (!is_null($data)) {
            $this->_getResource()->saveProductLinks($product, $data, self::LINK_TYPE_CROSSSELL);
        }

        return $this;
    }

    /**
     * Save grouped product relation links
     *
     * @param Mage_Catalog_Model_Product $product
     * @return $this
     */
    public function saveGroupedLinks($product)
    {
        $data = $product->getGroupedLinkData();
        if (!is_null($data)) {
            $this->_getResource()->saveGroupedLinks($product, $data, self::LINK_TYPE_GROUPED);
        }

        return $this;
    }
}
