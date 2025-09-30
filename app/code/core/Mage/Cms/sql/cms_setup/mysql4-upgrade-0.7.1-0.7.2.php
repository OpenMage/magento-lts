<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Cms
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->run("
    UPDATE `{$this->getTable('cms_page')}` SET `root_template` = 'two_columns_left' WHERE `root_template` LIKE 'left_column';
    UPDATE `{$this->getTable('cms_page')}` SET `root_template` = 'two_columns_right' WHERE `root_template` LIKE 'right_column';
    UPDATE `{$this->getTable('cms_page')}` SET `root_template` = 'three_columns' WHERE `root_template` LIKE 'three_column';
");

$installer->endSetup();
