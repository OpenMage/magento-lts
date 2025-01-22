<?php

/**
 * @category   Mage
 * @package    Mage_Customer
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2025 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var Mage_Customer_Model_Entity_Setup $installer */
$installer = $this;
$installer->startSetup();

$this->addAttribute('customer', 'created_at', [
    'type'     => 'static',
    'label'    => 'Created At',
    'visible'  => false,
    'required' => false,
    'input'    => 'date',
]);

$installer->endSetup();
