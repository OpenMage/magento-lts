<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Paypal
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
