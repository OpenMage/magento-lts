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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_GoogleBase
 * @copyright  Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

// encrypt google base passwords
$select = $installer->getConnection()->select()
    ->from($installer->getTable('core/config_data'))
    ->where('path LIKE ?', 'google/googlebase/password');
foreach ($installer->getConnection()->fetchAll($select) as $row) {
    $bind  = array(
        'value' => Mage::helper('core')->encrypt($row['value'])
    );
    $where = array(
        'config_id=?' => $row['config_id']
    );
    $installer->getConnection()->update($installer->getTable('core/config_data'), $bind, $where);
}
