<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

$installer = $this;

/** @var Mage_Sales_Model_Entity_Setup $installer */
$installer->installEntities();
$installer->startSetup();
$installer->endSetup();
