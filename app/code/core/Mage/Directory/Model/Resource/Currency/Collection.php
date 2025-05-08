<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Directory
 */

/**
 * Directory currency collection model
 *
 * @package    Mage_Directory
 * @deprecated  since 1.5.0.0
 */
class Mage_Directory_Model_Resource_Currency_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Currency name table
     *
     * @var string
     */
    protected $_currencyNameTable;

    /**
     * Currency rate table
     *
     * @var string
     */
    protected $_currencyRateTable;

    /**
     * Define resource model and tables
     */
    protected function _construct()
    {
        $this->_init('directory/currency');

        $this->_currencyNameTable   = $this->getTable('directory/currency_name');
        $this->_currencyRateTable   = $this->getTable('directory/currency_rate');
    }

    /**
     * Join currency rates by currency
     *
     * @param string $currency
     * @return $this
     */
    public function joinRates($currency)
    {
        $alias = sprintf('%s_rate', $currency);
        $this->addBindParam(':' . $alias, $currency);
        $this->_select
            ->joinLeft(
                [$alias => $this->_currencyRateTable],
                "{$alias}.currency_to = main_table.currency_code AND {$alias}.currency_from=:{$alias}",
                'rate',
            );

        return $this;
    }

    /**
     * Set language condition by name table
     *
     * @param string $lang
     * @return $this
     */
    public function addLanguageFilter($lang = null)
    {
        if (is_null($lang)) {
            $lang = Mage::app()->getStore()->getLanguageCode();
        }
        return $this->addFieldToFilter('main_table.language_code', $lang);
    }

    /**
     * Add currency code condition
     *
     * @param string $code
     * @return $this
     */
    public function addCodeFilter($code)
    {
        if (is_array($code)) {
            $this->addFieldToFilter('main_table.currency_code', ['in' => $code]);
        } else {
            $this->addFieldToFilter('main_table.currency_code', $code);
        }

        return $this;
    }

    /**
     * Convert collection items to select options array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_toOptionArray('currency_code', 'currency_name');
    }
}
