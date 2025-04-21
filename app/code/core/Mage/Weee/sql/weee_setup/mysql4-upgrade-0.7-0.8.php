<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Weee
 */

/** @var Mage_Weee_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->getConnection()->addConstraint('FK_WEEE_TAX_ATTRIBUTE_ID', $installer->getTable('weee_tax'), 'attribute_id', $installer->getTable('eav_attribute'), 'attribute_id');

$installer->endSetup();
