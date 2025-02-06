<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Core
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();
$installer->getConnection()->addColumn($installer->getTable('core/layout_update'), 'sort_order', "smallint(5) NOT NULL DEFAULT '0'");
$installer->endSetup();
