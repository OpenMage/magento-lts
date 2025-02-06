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

$conn = $installer->getConnection();
$table = $this->getTable('design_change');

try {
    $conn->addColumn($table, 'design', "varchar(255) not null default ''");
} catch (Exception $e) {
}

$conn->dropColumn($table, 'package');
$conn->dropColumn($table, 'theme');
