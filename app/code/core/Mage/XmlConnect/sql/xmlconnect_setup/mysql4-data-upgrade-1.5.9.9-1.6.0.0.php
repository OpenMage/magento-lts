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
 * @package     Mage_XmlConnect
 * @copyright  Copyright (c) 2006-2019 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Xmlconnect templates data update
 *
 * @category    Mage
 * @package     Mage_Xmlconnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$templateTableName = $installer->getTable('xmlconnect/template');
$appCodeField = $installer->getConnection()->tableColumnExists($templateTableName, 'app_code');

if ($appCodeField) {
    /** @var $appModel Mage_XmlConnect_Model_Application */
    $appModel = Mage::getModel('xmlconnect/application');
    /** @var $templateModel Mage_XmlConnect_Model_Template */
    $templateModel = Mage::getModel('xmlconnect/template');

    $select = $templateModel->getResource()->getReadConnection()->select()
        ->from($templateTableName, array('app_code', 'template_id'));

    $result = $templateModel->getResource()->getReadConnection()->fetchAll($select);

    foreach ($result as $rows) {
        if (empty($rows['app_code'])) {
            continue;
        }
        $appModel->loadByCode($rows['app_code']);
        $templateModel->load($rows['template_id']);
        $templateModel->setApplicationId($appModel->getApplicationId());
        $templateModel->save();
    }

    $installer->getConnection()->dropColumn($templateTableName, 'app_code');
}
