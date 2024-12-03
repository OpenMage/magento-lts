<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog Product Type Price Indexer interface
 *
 * @category   Mage
 * @package    Mage_Catalog
 */
interface Mage_Catalog_Model_Resource_Product_Indexer_Price_Interface
{
    /**
     * Reindex temporary (price result data) for all products
     *
     */
    public function reindexAll();

    /**
     * Reindex temporary (price result data) for defined product(s)
     *
     * @param int|array $entityIds
     */
    public function reindexEntity($entityIds);

    /**
     * Register data required by product type process in event object
     */
    public function registerEvent(Mage_Index_Model_Event $event);
}
