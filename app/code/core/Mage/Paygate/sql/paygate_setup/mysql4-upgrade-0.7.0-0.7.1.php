<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paygate
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

// replace transaction URLs - see http://integrationwizard.x.com/sdkupdate/step3.php
foreach ([
    'pilot-payflowpro.verisign.com' => 'pilot-payflowpro.paypal.com',
    'test-payflow.verisign.com'     => 'pilot-payflowpro.paypal.com',
    'payflow.verisign.com'          => 'payflowpro.paypal.com',
] as $from => $to
) {
    $installer->run("
    UPDATE {$installer->getTable('core/config_data')} SET `value` = REPLACE(`value`, '{$from}', '{$to}')
    WHERE `path` = 'payment/verisign/url'
    ");
}

$installer->endSetup();
