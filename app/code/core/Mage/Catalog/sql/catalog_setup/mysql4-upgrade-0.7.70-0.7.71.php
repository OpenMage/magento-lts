<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 */
$this->startSetup();
$this->getConnection()->query("DROP TABLE IF EXISTS `{$this->getTable('catalog/category_flat')}`;");
$this->endSetup();
