<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Config locale allowed currencies backend
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Model_System_Config_Backend_Locale extends Mage_Core_Model_Config_Data
{
    /**
     * Validate data before save data
     *
     * @return Mage_Core_Model_Abstract
     * @throws Mage_Core_Exception
     */
    protected function _beforeSave()
    {
        $allCurrenciesOptions = Mage::getSingleton('adminhtml/system_config_source_locale_currency_all')
            ->toOptionArray(true);

        $allCurrenciesValues = array_column($allCurrenciesOptions, 'value');

        foreach ($this->getValue() as $currency) {
            if (!in_array($currency, $allCurrenciesValues)) {
                Mage::throwException(Mage::helper('adminhtml')->__('Currency doesn\'t exist.'));
            }
        }

        return parent::_beforeSave();
    }

    /**
     * @return $this
     */
    protected function _afterSave()
    {
        $collection = Mage::getModel('core/config_data')
            ->getCollection()
            ->addPathFilter('currency/options');

        $values     = explode(',', $this->getValue());
        $exceptions = [];

        foreach ($collection as $data) {
            $match = false;
            $scopeName = Mage::helper('adminhtml')->__('Default scope');

            if (preg_match('/(base|default)$/', $data->getPath(), $match)) {
                if (!in_array($data->getValue(), $values)) {
                    $currencyName = Mage::app()->getLocale()->currency($data->getValue())->getName();
                    if ($match[1] == 'base') {
                        $fieldName = Mage::helper('adminhtml')->__('Base currency');
                    } else {
                        $fieldName = Mage::helper('adminhtml')->__('Display default currency');
                    }

                    switch ($data->getScope()) {
                        case 'default':
                            $scopeName = Mage::helper('adminhtml')->__('Default scope');
                            break;

                        case 'website':
                            $websiteName = Mage::getModel('core/website')->load($data->getScopeId())->getName();
                            $scopeName = Mage::helper('adminhtml')->__('website(%s) scope', $websiteName);
                            break;

                        case 'store':
                            $storeName = Mage::getModel('core/store')->load($data->getScopeId())->getName();
                            $scopeName = Mage::helper('adminhtml')->__('store(%s) scope', $storeName);
                            break;
                    }

                    $exceptions[] = Mage::helper('adminhtml')->__('Currency "%s" is used as %s in %s.', $currencyName, $fieldName, $scopeName);
                }
            }
        }
        if ($exceptions) {
            Mage::throwException(implode("\n", $exceptions));
        }

        return $this;
    }
}
