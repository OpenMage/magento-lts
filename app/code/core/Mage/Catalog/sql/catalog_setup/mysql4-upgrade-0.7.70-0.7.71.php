<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Catalog
 */

$this->startSetup();
$this->getConnection()->query("DROP TABLE IF EXISTS `{$this->getTable('catalog/category_flat')}`;");
$this->endSetup();
