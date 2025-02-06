<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Tag
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;

$installer->getConnection()
    ->addKey(
        $this->getTable('tag/relation'),
        'UNQ_TAG_CUSTOMER_PRODUCT_STORE',
        ['tag_id', 'customer_id', 'product_id', 'store_id'],
        'unique',
    );
