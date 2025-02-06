<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
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
