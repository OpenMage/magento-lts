<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Review
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;

//Fill table review/review_entity
$reviewEntityCodes = [
    Mage_Review_Model_Review::ENTITY_PRODUCT_CODE,
    Mage_Review_Model_Review::ENTITY_CUSTOMER_CODE,
    Mage_Review_Model_Review::ENTITY_CATEGORY_CODE,
];
foreach ($reviewEntityCodes as $entityCode) {
    $installer->getConnection()
            ->insert($installer->getTable('review/review_entity'), ['entity_code' => $entityCode]);
}

//Fill table review/review_entity
$reviewStatuses = [
    Mage_Review_Model_Review::STATUS_APPROVED       => 'Approved',
    Mage_Review_Model_Review::STATUS_PENDING        => 'Pending',
    Mage_Review_Model_Review::STATUS_NOT_APPROVED   => 'Not Approved'
];
foreach ($reviewStatuses as $k => $v) {
    $bind = [
        'status_id'     => $k,
        'status_code'   => $v
    ];
    $installer->getConnection()->insertForce($installer->getTable('review/review_status'), $bind);
}
