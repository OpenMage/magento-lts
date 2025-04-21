<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Cms
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer  = $this;
$installer->startSetup();

$installer->getConnection()->dropKey($this->getTable('cms/page'), 'identifier');

$installer->run("ALTER TABLE `{$this->getTable('cms/page')}` ADD KEY `identifier` (`identifier`)");

$installer->getConnection()->dropColumn($this->getTable('cms/page'), 'store_id');

$installer->getConnection()->dropColumn($this->getTable('cms/block'), 'store_id');

$installer->endSetup();
