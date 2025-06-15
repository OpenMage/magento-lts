<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Core
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;

$conn = $installer->getConnection();
$table = $this->getTable('design_change');

try {
    $conn->addColumn($table, 'design', "varchar(255) not null default ''");
} catch (Exception $e) {
}

$conn->dropColumn($table, 'package');
$conn->dropColumn($table, 'theme');
