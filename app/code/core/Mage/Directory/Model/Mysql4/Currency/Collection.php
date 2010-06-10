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
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Currency Mysql4 collection model
 *
 * @category   Mage
 * @package    Mage_Directory
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Directory_Model_Mysql4_Currency_Collection extends Varien_Data_Collection_Db
{
    protected $_currencyTable;
    protected $_currencyNameTable;
    protected $_currencyRateTable;

    public function __construct()
    {
        $resource = Mage::getSingleton('core/resource');
        parent::__construct($resource->getConnection('directory_read'));
        $this->_currencyTable       = $resource->getTableName('directory/currency');
        $this->_currencyNameTable   = $resource->getTableName('directory/currency_name');
        $this->_currencyRateTable   = $resource->getTableName('directory/currency_rate');

        $this->_select->from(array('main_table'=>$this->_currencyNameTable));
        /*$this->_select->join(array('name_table'=>$this->_currencyNameTable),
            "main_table.currency_code=name_table.currency_code");*/

        $this->setItemObjectClass(Mage::getConfig()->getModelClassName('directory/currency'));
    }

    public function joinRates($currency)
    {
        $alias = $currency.'_rate';
        $this->_select->joinLeft(array($alias=>$this->_currencyRateTable),
            $this->getConnection()->quoteInto("$alias.currency_to=main_table.currency_code AND $alias.currency_from=?", $currency),
            'rate');
        return $this;
    }

    /**
     * Set language condition by name table
     *
     * @param   string $lang
     * @return  Varien_Data_Collection_Db
     */
    public function addLanguageFilter($lang=null)
    {
        if (is_null($lang)) {
            $lang = Mage::app()->getStore()->getLanguageCode();
        }
        $this->addFilter('language', "main_table.language_code='$lang'", 'string');
        return $this;
    }

    /**
     * Add currency code condition
     *
     * @param   string $code
     * @return  Varien_Data_Collection_Db
     */
    public function addCodeFilter($code)
    {
        if (is_array($code)) {
            $this->addFilter("codes",
                $this->getConnection()->quoteInto("main_table.currency_code IN (?)", $code),
                'string'
            );
        }
        else {
            $this->addFilter("code_$code", "main_table.currency_code='$code'", 'string');
        }
        return $this;
    }

    public function toOptionArray()
    {
        return $this->_toOptionArray('currency_code', 'currency_name');
    }
}
