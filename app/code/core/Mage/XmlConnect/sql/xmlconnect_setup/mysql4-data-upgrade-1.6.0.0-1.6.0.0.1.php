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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @copyright   Copyright (c) 2014 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Xmlconnect Config data install
 *
 * @category    Mage
 * @package     Mage_Xmlconnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

    $configTableName = $installer->getTable('xmlconnect/configData');

    /** @var $configModel Mage_XmlConnect_Model_Application */
    $configModel = Mage::getModel('xmlconnect/configData');
    $select = $configModel->getResource()->getReadConnection()->select()->from(
        $configTableName, array('application_id')
    )->group('application_id')->where('category=?', Mage_XmlConnect_Model_Application::DEPRECATED_CONFIG_FLAG);

    $result = $configModel->getResource()->getReadConnection()->fetchCol($select);

    if (count($result)) {
        Mage::getModel('xmlconnect/images')->dataUpgradeOldConfig($result);
        Mage::getModel('xmlconnect/configData')->pagesUpgradeOldConfig($result);
    }
