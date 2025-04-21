<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Weee
 */

/** @var Mage_Weee_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->addAttribute('quote_item', 'weee_tax_applied', ['type' => 'text']);
$installer->addAttribute('order_item', 'weee_tax_applied', ['type' => 'text']);

$installer->endSetup();
