<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * @package    Mage_Adminhtml
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
            foreach ($services as $code => $options) {
                if (isset($currencyConfig[$code]['active']) && $currencyConfig[$code]['active'] === '0') {
                    continue;
                }

                $this->_options[] = [
                    'label' => $options['name'],
                    'value' => $code,
                ];
            }
        }

        return $this->_options;
    }
}
