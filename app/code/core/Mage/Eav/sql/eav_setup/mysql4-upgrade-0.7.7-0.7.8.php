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

$installer->getConnection()->addColumn($installer->getTable('eav/attribute'), 'is_visible_in_advanced_search', "TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT '0'");

$installer->endSetup();
