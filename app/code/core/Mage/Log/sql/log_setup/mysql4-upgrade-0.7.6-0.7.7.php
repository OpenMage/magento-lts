<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Log
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->run("
    ALTER TABLE `{$installer->getTable('log/customer')}` ENGINE INNODB;
    ALTER TABLE `{$installer->getTable('log/quote_table')}` ENGINE INNODB;
    ALTER TABLE `{$installer->getTable('log/summary_table')}` ENGINE INNODB;
    ALTER TABLE `{$installer->getTable('log/summary_type_table')}` ENGINE INNODB;
    ALTER TABLE `{$installer->getTable('log/url_table')}` ENGINE INNODB;
    ALTER TABLE `{$installer->getTable('log/url_info_table')}` ENGINE INNODB;
    ALTER TABLE `{$installer->getTable('log/visitor')}` ENGINE INNODB;
    ALTER TABLE `{$installer->getTable('log/visitor_info')}` ENGINE INNODB;
");

$installer->endSetup();
