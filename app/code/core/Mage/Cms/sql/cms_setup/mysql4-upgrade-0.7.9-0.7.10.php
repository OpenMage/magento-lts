<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Cms
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer  = $this;

$pageTable = $installer->getTable('cms/page');
$blockTable = $installer->getTable('cms/block');

$installer->getConnection()->modifyColumn($pageTable, 'content', 'MEDIUMTEXT');
$installer->getConnection()->modifyColumn($blockTable, 'content', 'MEDIUMTEXT');
