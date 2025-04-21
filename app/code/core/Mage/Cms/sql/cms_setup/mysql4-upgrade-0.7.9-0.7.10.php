<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Cms
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer  = $this;

$pageTable = $installer->getTable('cms/page');
$blockTable = $installer->getTable('cms/block');

$installer->getConnection()->modifyColumn($pageTable, 'content', 'MEDIUMTEXT');
$installer->getConnection()->modifyColumn($blockTable, 'content', 'MEDIUMTEXT');
