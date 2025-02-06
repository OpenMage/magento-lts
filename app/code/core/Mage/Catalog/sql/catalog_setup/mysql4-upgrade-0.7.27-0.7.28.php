<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Catalog
 */

/** @var Mage_Catalog_Model_Resource_Setup  $installer */
$installer = $this;

$installer->run("UPDATE `{$installer->getTable('eav_attribute')}` SET `position` = 1 WHERE `position` = 0 AND `attribute_code` != 'price';");
