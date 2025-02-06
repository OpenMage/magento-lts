<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Review
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer  = $this;
$installer->startSetup();
$installer->getConnection()->dropForeignKey($this->getTable('review'), 'FK_REVIEW_PARENT_PRODUCT');
$installer->endSetup();
