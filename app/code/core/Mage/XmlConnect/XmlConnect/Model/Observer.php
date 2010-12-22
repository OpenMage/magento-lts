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
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * XmlConnect module observer
 *
 * @author  Magento Mobile Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Model_Observer
{
    /**
     * List of config field names which changing affects mobile applications behaviour
     *
     * @var array
     */
    protected $_appDependOnConfigFieldPathes = array(
        'paypal/general/business_account',
        'sendfriend/email/max_recipients',
        'sendfriend/email/allow_guest',
        'general/locale/code',
        'currency/options/default'
    );

    /**
     * Update all applications "updated at" parameter with current date on save some configurations
     *
     * @param Varien_Event_Observer $observer
     */
    public function changeUpdatedAtParamOnConfigSave($observer)
    {
        $configData = $observer->getEvent()->getConfigData();
        if ($configData && (int)$configData->isValueChanged() && in_array($configData->getPath(), $this->_appDependOnConfigFieldPathes)) {
            Mage::getModel('xmlconnect/application')->updateAllAppsUpdatedAtParameter();
        }
    }
}
