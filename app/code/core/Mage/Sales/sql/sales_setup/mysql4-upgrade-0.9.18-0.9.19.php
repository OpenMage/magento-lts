<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @var Mage_Sales_Model_Resource_Setup $installer
 */
$installer = $this;

$installer->run("
    ALTER TABLE `{$this->getTable('sales_order')}` ADD INDEX `IDX_INCREMENT_ID` (`increment_id`);
");
