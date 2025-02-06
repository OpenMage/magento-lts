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
$installer->getConnection()->addColumn($installer->getTable('core/email_template'), 'orig_template_code', 'VARCHAR(200) DEFAULT NULL');
$installer->endSetup();
