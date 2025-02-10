<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @license Open Software License (OSL 3.0)
 * @var Mage_Sales_Model_Entity_Setup $installer
 */
$installer = $this;

$installer->addAttribute('order', 'edit_increment', ['type' => 'int']);
$installer->addAttribute('order', 'original_increment_id', ['type' => 'varchar']);
