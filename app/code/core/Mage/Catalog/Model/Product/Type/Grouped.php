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
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Grouped product type implementation
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Product_Type_Grouped extends Mage_Catalog_Model_Product_Type_Abstract
{
    const TYPE_CODE = 'grouped';
    protected $_associatedProducts  = null;
    protected $_associatedProductIds= null;
    protected $_statusFilters = null;
    protected $_isComposite = true;

    /**
     * Retrieve array of associated products
     *
     * @return array
     */
    public function getAssociatedProducts()
    {
        if (is_null($this->_associatedProducts)) {
            $this->_associatedProducts = array();

            if (!Mage::app()->getStore()->isAdmin()) {
                $this->setSaleableStatus();
            }

            $collection = $this->getAssociatedProductCollection()
                ->addAttributeToSelect('*')
                ->addFilterByRequiredOptions()
                ->setPositionOrder()
                ->addStoreFilter($this->getStoreFilter())
                ->addAttributeToFilter('status', array('in' => $this->getStatusFilters()));

            foreach ($collection as $product) {
                $this->_associatedProducts[] = $product;
            }
        }
        return $this->_associatedProducts;
    }

    /**
     *  Add status filter to collection
     *
     *  @param    int $status
     *  @return	  void
     */
    public function addStatusFilter ($status)
    {
        $this->_statusFilters[] = $status;
        return $this;
    }

    /**
     *  Set only saleable filter
     *
     *  @param    none
     *  @return	  void
     */
    public function setSaleableStatus ()
    {
        $this->_statusFilters = Mage::getSingleton('catalog/product_status')->getSaleableStatusIds();
        return $this;
    }

    /**
     *  Return all assigned status filters
     *
     *  @param    none
     *  @return	  void
     */
    public function getStatusFilters ()
    {
        if ($this->_statusFilters === null) {
            return array(
                Mage_Catalog_Model_Product_Status::STATUS_ENABLED,
                Mage_Catalog_Model_Product_Status::STATUS_DISABLED
            );
        }
        return $this->_statusFilters;
    }

    /**
     * Retrieve related products identifiers
     *
     * @return array
     */
    public function getAssociatedProductIds()
    {
        if (is_null($this->_associatedProductIds)) {
            $this->_associatedProductIds = array();
            foreach ($this->getAssociatedProducts() as $product) {
                $this->_associatedProductIds[] = $product->getId();
            }
        }
        return $this->_associatedProductIds;
    }

    /**
     * Retrieve collection of associated products
     */
    public function getAssociatedProductCollection()
    {
        $collection = $this->getProduct()->getLinkInstance()->useGroupedLinks()
            ->getProductCollection()
            ->setIsStrongMode();
        $collection->setProduct($this->getProduct());
        return $collection;
    }

    /**
     * Check is product available for sale
     *
     * @return bool
     */
    public function isSalable()
    {
        $salable = $this->getProduct()->getIsSalable();
        if (!is_null($salable) && !$salable) {
            return $salable;
        }

        $salable = false;
        foreach ($this->getAssociatedProducts() as $product) {
            $salable = $salable || $product->isSalable();
        }
        return $salable;
    }

    /**
     * Save type related data
     *
     * @return unknown
     */
    public function save()
    {
        parent::save();
        $this->getProduct()->getLinkInstance()->saveGroupedLinks($this->getProduct());
        return $this;
    }

    /**
     * Initialize product(s) for add to cart process
     *
     * @param   Varien_Object $buyRequest
     * @return  array || string || false
     */
    public function prepareForCart(Varien_Object $buyRequest)
    {
        $productsInfo = $buyRequest->getSuperGroup();
        if (!empty($productsInfo) && is_array($productsInfo)) {
            $products = array();
            if ($associatedProducts = $this->getAssociatedProducts()) {
                $productId = $this->getProduct()->getId();
                foreach ($associatedProducts as $subProduct) {
                    if(isset($productsInfo[$subProduct->getId()])) {
                        $qty = $productsInfo[$subProduct->getId()];
                        if (!empty($qty)) {
                            $subProduct->setCartQty($qty);
                            $subProduct->addCustomOption('product_type', self::TYPE_CODE, $this->getProduct());
                            $products[] = $subProduct;
                        }
                    }
                }
            }
            if (count($products)) {
                return $products;
            }
        }
        return Mage::helper('catalog')->__('Please specify the product(s) quantity');
    }
}