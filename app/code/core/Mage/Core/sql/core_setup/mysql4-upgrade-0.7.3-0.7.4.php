<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Core
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

$rows = $installer->getConnection()->fetchAll(
    "select * from {$this->getTable('core_config_data')} where
    path in (
    'paypal/wpp/api_password', 'paypal/wpp/api_signature', 'paypal/wpp/api_username',
    'paypal/wps/business_account', 'paypal/wpuk/user', 'paypal/wpuk/pwd', 'carriers/dhl/id',
    'carriers/dhl/password', 'carriers/dhl/shipping_key', 'carriers/dhl/shipping_intlkey',
    'carriers/fedex/account', 'carriers/ups/account_license_number', 'carriers/ups/username',
    'carriers/usps/userid', 'payment/authorizenet/login', 'payment/authorizenet/trans_key',
    'payment/verisign/pwd', 'payment/verisign/user')",
);

$hlp = Mage::helper('core');
foreach ($rows as $r) {
    if (!empty($r['value'])) {
        $r['value'] = $hlp->encrypt($r['value']);
        $installer->getConnection()->update($this->getTable('core_config_data'), $r, 'config_id=' . $r['config_id']);
    }
}
$installer->endSetup();
