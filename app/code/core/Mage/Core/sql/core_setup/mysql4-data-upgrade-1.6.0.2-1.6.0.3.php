<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Core
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$connection = $installer->getConnection();
$connection->update($installer->getTable('core/translate'), [
    'crc_string' => new Zend_Db_Expr('CRC32(' . $connection->quoteIdentifier('string') . ')'),
]);
