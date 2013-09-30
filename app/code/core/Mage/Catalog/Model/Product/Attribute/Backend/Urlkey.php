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
 * @package     Mage_Catalog
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Product url key attribute backend
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Product_Attribute_Backend_Urlkey extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{
    /**
     * Format url_key value
     *
     * @param Mage_Catalog_Model_Product $object
     * @return Mage_Catalog_Model_Product_Attribute_Backend_Urlkey
     */
    public function beforeSave($object)
    {
        $attributeName = $this->getAttribute()->getName();

        $urlKey = $object->getData($attributeName);
        if ($urlKey === false) {
            return $this;
        }
        if ($urlKey == '') {
            $urlKey = $object->getName();
        }
        $urlKey = $object->formatUrlKey($urlKey);
        if (empty($urlKey)) {
            $urlKey = Mage::helper('core')->uniqHash();
        }
        $object->setData($attributeName, $urlKey);

        $this->_validateUrlKey($object);
        return $this;
    }

    /**
     * Check unique url_key value in catalog_product_entity_url_key table.
     *
     * @param Mage_Catalog_Model_Product $object
     * @return Mage_Catalog_Model_Product_Attribute_Backend_Urlkey
     * @throws Mage_Core_Exception
     */
    protected function _validateUrlKey($object)
    {
        $connection = $object->getResource()->getReadConnection();

        $select = $connection->select()
            ->from($this->getAttribute()->getBackendTable(), array('count' => new Zend_Db_Expr('COUNT(\'value_id\')')))
            ->where($connection->quoteInto('entity_id <> ?', $object->getId()))
            ->where($connection->quoteInto('value = ?', $object->getUrlKey()));
        $result = $connection->fetchOne($select);
        if ((int)$result) {
           throw new Mage_Core_Exception(
               Mage::helper('catalog')->__("Product with the '%s' url_key attribute already exists.",
                   $object->getUrlKey())
           );
        }

        return $this;
    }
}
