<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

declare(strict_types=1);

/** @var Mage_Paypal_Model_Resource_Setup $this */
$installer = $this;
$installer->startSetup();

// The JavaScript SDK v6 buttons have no shape, color, layout or tagline options.
$installer->getConnection()->delete(
    $installer->getTable('core/config_data'),
    ['path IN (?)' => [
        'payment/paypal/button_shape',
        'payment/paypal/button_color',
        'payment/paypal/button_layout',
        'payment/paypal/button_message',
    ]],
);

$installer->endSetup();
