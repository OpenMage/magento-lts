<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category    Phoenix
 * @package     Phoenix_Moneybookers
 * @copyright   Copyright (c) 2016 Phoenix Medien GmbH & Co. KG (http://www.phoenix-medien.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();
$conn = $installer->getConnection();

$select = $conn
    ->select()
    ->from($this->getTable('core/config_data'), array('scope', 'scope_id', 'path', 'value'))
    ->where(new Zend_Db_Expr("path LIKE 'moneybookers/moneybookers%'"));
$data = $conn->fetchAll($select);

if (!empty($data)) {
    foreach ($data as $key => $value) {
        $data[$key]['path'] = preg_replace('/^moneybookers\/moneybookers/', 'payment/moneybookers', $value['path']);
    }
    $conn->insertOnDuplicate($this->getTable('core/config_data'), $data, array('path'));
    $conn->delete($this->getTable('core/config_data'), new Zend_Db_Expr("path LIKE 'moneybookers/moneybookers%'"));
}

$installer->endSetup();
