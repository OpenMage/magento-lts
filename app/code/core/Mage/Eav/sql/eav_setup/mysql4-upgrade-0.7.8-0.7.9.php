<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Eav
 */

/** @var Mage_Eav_Model_Entity_Setup $installer */
$installer  = $this;
$installer->startSetup();

$installer->run("
ALTER TABLE {$installer->getTable('eav_attribute_group')}
    CHANGE `attribute_group_name` `attribute_group_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL default '';
");

$installer->endSetup();
