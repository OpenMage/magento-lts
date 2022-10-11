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
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mage_Adminhtml_Model_System_Config_Source_Currency_Service
{
    protected $_options;

    public function toOptionArray($isMultiselect)
    {
        if (!$this->_options) {
            $services = Mage::getConfig()->getNode('global/currency/import/services')->asArray();
            $currencyConfig = Mage::getStoreConfig('currency');
            $this->_options = [];
            foreach ($services as $_code => $_options) {
                if (isset($currencyConfig[$_code]['active']) && $currencyConfig[$_code]['active'] === '0') {
                    continue;
                }
                $this->_options[] = [
                    'label' => $_options['name'],
                    'value' => $_code,
                ];
            }
        }

        return $this->_options;
    }
}
