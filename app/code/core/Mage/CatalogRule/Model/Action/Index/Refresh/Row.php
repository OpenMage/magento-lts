<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_CatalogRule
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2023 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog rule indexer for row
 *
 * @category   Mage
 * @package    Mage_CatalogRule
 */
class Mage_CatalogRule_Model_Action_Index_Refresh_Row extends Mage_CatalogRule_Model_Action_Index_Refresh
{
    /**
     * Product Id
     *
     * @var int
     */
    protected $_productId;

    /**
     * Constructor with parameters
     * Array of arguments with keys
     *  - 'connection' Varien_Db_Adapter_Interface
     *  - 'factory' Mage_Core_Model_Factory
     *  - 'resource' Mage_Core_Model_Resource_Db_Abstract
     *  - 'app' Mage_Core_Model_App
     *  - 'value' int|Mage_Catalog_Model_Product
     *
     * @param array $args
     */
    public function __construct(array $args)
    {
        parent::__construct($args);
        $this->_productId = $args['value'] instanceof Mage_Catalog_Model_Product
            ? $args['value']->getId()
            : $args['value'];
    }

    /**
     * Do not recreate rule group website for row refresh
     * @param string $timestamp
     */
    protected function _prepareGroupWebsite($timestamp)
    {
    }

    /**
     * Prepare temporary data
     *
     * @param Mage_Core_Model_Website $website
     * @return Varien_Db_Select
     */
    protected function _prepareTemporarySelect(Mage_Core_Model_Website $website)
    {
        $select = parent::_prepareTemporarySelect($website);
        return $select->where('rp.product_id IN (?)', $this->_productId);
    }

    /**
     * Remove old index data
     *
     * @param Mage_Core_Model_Website $website
     */
    protected function _removeOldIndexData(Mage_Core_Model_Website $website)
    {
        $this->_connection->query(
            $this->_connection->deleteFromSelect(
                $this->_connection->select()
                    ->from($this->_resource->getTable('catalogrule/rule_product_price'))
                    ->where('product_id IN (?)', $this->_productId)
                    ->where('website_id = ?', $website->getId()),
                $this->_resource->getTable('catalogrule/rule_product_price')
            )
        );
    }

    /**
     * Return data for affected product
     *
     * @return int
     */
    protected function _getProduct()
    {
        return $this->_productId;
    }
}
