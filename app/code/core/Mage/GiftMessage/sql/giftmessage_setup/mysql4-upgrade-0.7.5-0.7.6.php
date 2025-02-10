<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @var Mage_GiftMessage_Model_Resource_Setup $this
 */
$installer = $this;

$installer->updateAttribute(
    'catalog_product',
    'gift_message_available',
    'frontend_input_renderer',
    'giftmessage/adminhtml_product_helper_form_config',
);
