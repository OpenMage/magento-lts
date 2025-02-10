<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @var Mage_Customer_Model_Entity_Setup $installer
 */
$installer = $this;

$this->updateAttribute('customer_address', 'region_id', 'frontend_label', 'State/Province');
