<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Weee
 */

/** @var Mage_Weee_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->getConnection()->addConstraint('FK_WEEE_TAX_ATTRIBUTE_ID', $installer->getTable('weee_tax'), 'attribute_id', $installer->getTable('eav_attribute'), 'attribute_id');

$installer->endSetup();
