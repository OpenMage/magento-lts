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
/** @var $this Mage_Catalog_Model_Resource_Setup */

$this->startSetup();
$connection = $this->getConnection();

foreach (array('catalog/product', 'catalog/category') as $tableName) {
    $select = $connection->select()
        ->from(array('ev' => $this->getTable(array($tableName, 'varchar'))),
            array('entity_type_id', 'attribute_id', 'store_id', 'entity_id', 'value'))
        ->join(
            array('ea' => $this->getTable('eav/attribute')),
            $connection->quoteIdentifier('ea.attribute_id') . ' = ' .
                $connection->quoteIdentifier('ev.attribute_id') . ' AND ' .
                $connection->quoteInto($connection->quoteIdentifier('ea.attribute_code') . ' = ?', 'url_key'),
            array()
        );

    $insertQuery = $connection->insertFromSelect($select, $this->getTable(array($tableName, 'url_key')),
        array('entity_type_id', 'attribute_id', 'store_id', 'entity_id', 'value')
    );

    $insertQuery .= sprintf('ON DUPLICATE KEY UPDATE %1$s = values(%1$s), %2$s = values(%2$s)',
        $connection->quoteIdentifier('store_id'),
        $connection->quoteIdentifier('entity_id')
    );

    $connection->query($insertQuery);
}

$this->endSetup();
