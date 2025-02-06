<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
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
