<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @var Mage_Eav_Model_Entity_Setup $installer
 */
$installer  = $this;
$installer->startSetup();

$installer->getConnection()->addColumn($installer->getTable('eav_attribute_group'), 'default_id', 'SMALLINT( 5 ) NOT NULL');
$installer->installDefaultGroupIds();

$installer->endSetup();
