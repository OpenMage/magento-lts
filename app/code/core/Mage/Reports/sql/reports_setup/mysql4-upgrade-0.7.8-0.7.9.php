<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Reports
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();
$installer->run("ALTER TABLE {$this->getTable('report_viewed_product_index')} CHANGE `visitor_id` `visitor_id` INT( 10 ) UNSIGNED NULL ");
$installer->endSetup();
