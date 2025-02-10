<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @var Mage_Core_Model_Resource_Setup $installer
 */
$installer = $this;
$installer->startSetup();

$installer->run("

ALTER TABLE {$this->getTable('core_url_rewrite')}
ADD COLUMN `type` int(1) NOT NULL  DEFAULT '0' after `options`,
ADD COLUMN `description` varchar(255) NULL  after `type`;

");

$installer->endSetup();
