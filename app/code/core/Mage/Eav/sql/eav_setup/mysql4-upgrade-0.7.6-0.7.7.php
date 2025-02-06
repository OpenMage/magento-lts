<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Eav
 */

/** @var Mage_Eav_Model_Entity_Setup $installer */
$installer  = $this;
$installer->startSetup();

$installer->run("
ALTER TABLE {$installer->getTable('eav_attribute_group')}
    CHANGE `default_id` `default_id` smallint unsigned NULL default '0';
");

$installer->endSetup();
