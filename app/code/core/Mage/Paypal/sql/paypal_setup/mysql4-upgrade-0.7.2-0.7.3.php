<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

/** @var Mage_Paypal_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->addAttribute('order_payment', 'cc_secure_verify', []);

// move paypal style settings to new paths
foreach ([
    'paypal/wpp/page_style' => 'paypal/style/page_style',
    'paypal/wps/logo_url' => 'paypal/style/logo_url',
] as $from => $to
) {
    $installer->run("
    UPDATE {$installer->getTable('core/config_data')} SET `path` = '{$to}'
    WHERE `path` = '{$from}'
    ");
}

$installer->endSetup();
