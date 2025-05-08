<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

/** @var Mage_Core_Model_Resource_Setup $this */
$installer = $this;
$connection = $installer->getConnection();
$select = $connection->select()
    ->from(
        ['config' => $installer->getTable('core/config_data')],
        ['scope_id' => 'config.scope_id'],
    )
    ->where('config.path=?', 'paypal/general/merchant_country')
    ->where('config.value<>?', 'US');

$result = $connection->fetchAll($select);
foreach ($result as $row) {
    $connection->delete(
        $installer->getTable('core/config_data'),
        'path LIKE "%express_bml%"'
        . $connection->quoteInto(' AND scope_id = ?', $row['scope_id']),
    );
}

$installer->endSetup();
