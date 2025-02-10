<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @var Mage_Core_Model_Resource_Setup $installer
 */
$installer = $this;
$installer->startSetup();

$installer->run("CREATE INDEX entity_id ON {$this->getTable('core/url_rewrite')} (entity_id);");

$installer->endSetup();
