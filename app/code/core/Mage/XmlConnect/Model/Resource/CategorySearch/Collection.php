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
 * @package     Mage_XmlConnect
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Category search collection
 *
 * @category   Mage
 * @package    Mage_XmlConnect
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Model_Resource_CategorySearch_Collection extends Mage_Catalog_Model_Resource_Category_Collection
{
    /**
     * Filter for category collection
     *
     * @var array
     */
    protected $_collectionFilter = array();

    /**
     * Add search query filter
     *
     * @param string $query
     * @return Mage_XmlConnect_Model_Resource_CategorySearch_Collection
     */
    public function addSearchFilter($query)
    {
        $this->_addNameFilter($query)->_addDescriptionFilter($query)
            ->_addUrlKeyFilter($query)->addFieldToFilter($this->getCollectionFilter());

        return $this;
    }

    /**
     * Add name filter
     *
     * @param string $query
     * @return Mage_XmlConnect_Model_Resource_CategorySearch_Collection
     */
    protected function _addNameFilter($query)
    {
        $collectionFilter = $this->getCollectionFilter();
        $collectionFilter[] = array('attribute' => 'name', 'like' => $query . '%');
        $this->setCollectionFilter($collectionFilter);
        return $this;
    }

    /**
     * Add description filter
     *
     * @param string $query
     * @return Mage_XmlConnect_Model_Resource_CategorySearch_Collection
     */
    protected function _addDescriptionFilter($query)
    {
        $collectionFilter = $this->getCollectionFilter();
        $collectionFilter[] = array('attribute' => 'description', 'like' => $query . '%');
        $this->setCollectionFilter($collectionFilter);
        return $this;
    }

    /**
     * Add url key filter
     *
     * @param string $query
     * @return Mage_XmlConnect_Model_Resource_CategorySearch_Collection
     */
    protected function _addUrlKeyFilter($query)
    {
        $collectionFilter = $this->getCollectionFilter();
        $collectionFilter[] = array('attribute' => 'url_key', 'like' => $query . '%');
        $this->setCollectionFilter($collectionFilter);
        return $this;
    }

    /**
     * Set collection filter
     *
     * @param array $collectionFilter
     * @return Mage_XmlConnect_Model_Resource_CategorySearch_Collection
     */
    public function setCollectionFilter($collectionFilter)
    {
        $this->_collectionFilter = $collectionFilter;
        return $this;
    }

    /**
     * Get collection filter
     *
     * @return array
     */
    public function getCollectionFilter()
    {
        return $this->_collectionFilter;
    }
}
