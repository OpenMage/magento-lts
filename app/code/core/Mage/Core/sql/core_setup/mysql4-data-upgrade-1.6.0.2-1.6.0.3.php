<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Core
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$connection = $installer->getConnection();
$connection->update($installer->getTable('core/translate'), [
    'crc_string' => new Zend_Db_Expr('CRC32(' . $connection->quoteIdentifier('string') . ')'),
]);
