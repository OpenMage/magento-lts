<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 * @category   Mage
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
