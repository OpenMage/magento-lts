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
 * @category    Mage
 * @package     Mage_XmlConnect
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Search Catalog Model
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Model_Adminhtml_Search_Catalog extends Varien_Object
{
    /**
     * Load search results
     *
     * @return Mage_XmlConnect_Model_Adminhtml_Search_Catalog
     */
    public function load()
    {
        $arr = array();

        if (!$this->hasStart() || !$this->hasLimit() || !$this->hasQuery()) {
            $this->setResults($arr);
            return $this;
        }

        $collection = Mage::helper('catalogsearch')->getQuery()->getSearchCollection()
            ->addAttributeToSelect('product_id')->addAttributeToSelect('name')->addAttributeToSelect('description')
            ->addAttributeToSelect('image')->addSearchFilter($this->getQuery())->setCurPage($this->getStart())
            ->setPageSize($this->getLimit())->load();

        foreach ($collection as $product) {
            $description = Mage::helper('core')->stripTags($product->getDescription());
            $arr[] = array(
                'id'            => 'product/1/' . $product->getId(),
                'item_id'       => $product->getId(),
                'type'          => Mage_XmlConnect_Model_ImageAction::ACTION_TYPE_PRODUCT,
                'label'         => Mage::helper('adminhtml')->__('Product'),
                'name'          => $product->getName(),
                'image'         => $product->getImage(),
                'description'   => Mage::helper('core/string')->substr($description, 0, 30),
                'url' => Mage::helper('adminhtml')->getUrl('*/catalog_product/edit', array('id' => $product->getId())),
            );
        }
        $this->setResults($arr);
        return $this;
    }
}
