<?php
/**
 * #5043 fix: Customer email - can't be changed in admin interface
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @see mysql4-upgrade-0.7.2-0.7.3.php
 */
/** @var Mage_Customer_Model_Entity_Setup $installer */
$installer = $this;
$installer->startSetup();

$attributeId = $installer->getAttributeId('customer', 'email');

$installer->run("
    DELETE FROM {$this->getTable('customer_entity_varchar')}
    WHERE attribute_id={$attributeId};
");

$installer->endSetup();
