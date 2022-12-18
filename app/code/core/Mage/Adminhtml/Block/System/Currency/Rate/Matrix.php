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
 * @copyright  Copyright (c) 2020-2021 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Manage currency block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_System_Currency_Rate_Matrix extends Mage_Adminhtml_Block_Template
{
    public function __construct()
    {
        $this->setTemplate('system/currency/rate/matrix.phtml');
    }

    protected function _prepareLayout()
    {
        $newRates = Mage::getSingleton('adminhtml/session')->getRates();
        Mage::getSingleton('adminhtml/session')->unsetData('rates');

        $currencyModel = Mage::getModel('directory/currency');
        $currencies = $currencyModel->getConfigAllowCurrencies();
        $defaultCurrencies = $currencyModel->getConfigBaseCurrencies();
        $oldCurrencies = $this->_prepareRates($currencyModel->getCurrencyRates($defaultCurrencies, $currencies));

        foreach ($currencies as $currency) {
            foreach ($oldCurrencies as $key => $value) {
                if (!array_key_exists($currency, $oldCurrencies[$key])) {
                    $oldCurrencies[$key][$currency] = '';
                }
            }
        }

        foreach ($oldCurrencies as $key => $value) {
            ksort($oldCurrencies[$key]);
        }

        sort($currencies);

        $this->setAllowedCurrencies($currencies)
            ->setDefaultCurrencies($defaultCurrencies)
            ->setOldRates($oldCurrencies)
            ->setNewRates($this->_prepareRates($newRates));

        return parent::_prepareLayout();
    }

    protected function getRatesFormAction()
    {
        return $this->getUrl('*/*/saveRates');
    }

    protected function _prepareRates($array)
    {
        if (!is_array($array)) {
            return $array;
        }

        foreach ($array as $key => $rate) {
            foreach ($rate as $code => $value) {
                $parts = explode('.', $value);
                if (count($parts) === 2) {
                    $parts[1] = str_pad(rtrim($parts[1], 0), 4, '0', STR_PAD_RIGHT);
                    $array[$key][$code] = implode('.', $parts);
                } elseif ($value > 0) {
                    $array[$key][$code] = number_format($value, 4);
                } else {
                    $array[$key][$code] = null;
                }
            }
        }
        return $array;
    }
}
