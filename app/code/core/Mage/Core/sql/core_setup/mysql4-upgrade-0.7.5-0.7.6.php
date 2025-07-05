<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Core
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer  = $this;
$installer->startSetup();

$installer->run("
ALTER TABLE {$this->getTable('design_change')}
  DROP `package`,
  DROP `theme`;
ALTER TABLE {$this->getTable('design_change')} ADD `design` VARCHAR( 255 ) NOT NULL AFTER `store_id` ;
");

$installer->endSetup();
