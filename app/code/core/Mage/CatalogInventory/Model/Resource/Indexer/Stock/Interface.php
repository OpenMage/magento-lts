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
 * @package     Mage_CatalogInventory
 * @copyright  Copyright (c) 2006-2019 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * CatalogInventory Stock Indexer Interface
 *
 * @category    Mage
 * @package     Mage_CatalogInventory
 * @author      Magento Core Team <core@magentocommerce.com>
 */
interface Mage_CatalogInventory_Model_Resource_Indexer_Stock_Interface
{
    /**
     * Reindex all stock status data
     *
     */
    public function reindexAll()
;

    /**
     * Reindex stock status data for defined ids
     *
     * @param int|array $entityIds
     */
    public function reindexEntity($entityIds)
;

    /**
     * Set Product Type Id for indexer
     *
     * @param string $typeId
     */
    public function setTypeId($typeId)
;

    /**
     * Retrieve Product Type Id for indexer
     *
     * @throws Mage_Core_Exception
     *
     */
    public function getTypeId()
;
}
