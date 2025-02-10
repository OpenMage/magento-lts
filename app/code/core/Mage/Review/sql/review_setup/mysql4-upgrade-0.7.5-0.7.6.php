<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @var Mage_Core_Model_Resource_Setup $installer
 */
$installer  = $this;
$installer->startSetup();
$installer->getConnection()->dropForeignKey($this->getTable('review'), 'FK_REVIEW_PARENT_PRODUCT');
$installer->endSetup();
