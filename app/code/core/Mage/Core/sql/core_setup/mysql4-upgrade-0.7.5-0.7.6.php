<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
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
