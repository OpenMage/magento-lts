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
 * @package     Mage_Directory
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Directory currency collection model
 *
 * @deprecated  since 1.5.0.0
 * @category    Mage
 * @package     Mage_Directory
 * @author      Magento Core Team <core@magentocommerce.com>
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
     *
     * @return void
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
     * @return Mage_Directory_Model_Resource_Currency_Collection
     */
    public function joinRates($currency)
    {
        $alias = sprintf('%s_rate', $currency);
        $this->addBindParam(':'.$alias, $currency);
        $this->_select
            ->joinLeft(
                array($alias => $this->_currencyRateTable),
                "{$alias}.currency_to = main_table.currency_code AND {$alias}.currency_from=:{$alias}",
                'rate');

        return $this;
    }

    /**
     * Set language condition by name table
     *
     * @param string $lang
     * @return Mage_Directory_Model_Resource_Currency_Collection
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
     * @return Mage_Directory_Model_Resource_Currency_Collection
     */
    public function addCodeFilter($code)
    {
        if (is_array($code)) {
            $this->addFieldToFilter("main_table.currency_code", array('in' => $code));
        } else {
            $this->addFieldToFilter("main_table.currency_code", $code);
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
