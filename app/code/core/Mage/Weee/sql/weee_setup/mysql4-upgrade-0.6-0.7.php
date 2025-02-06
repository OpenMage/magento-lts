<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Weee
 */

/** @var Mage_Weee_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->addAttribute('invoice_item', 'weee_tax_applied', ['type' => 'text']);
$installer->addAttribute('creditmemo_item', 'weee_tax_applied', ['type' => 'text']);

$installer->endSetup();
