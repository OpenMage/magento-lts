<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @var Mage_Weee_Model_Resource_Setup $installer
 */
$installer = $this;
$installer->startSetup();

$installer->addAttribute('invoice_item', 'weee_tax_applied', ['type' => 'text']);
$installer->addAttribute('creditmemo_item', 'weee_tax_applied', ['type' => 'text']);

$installer->endSetup();
