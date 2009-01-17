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
 * @category   Mage
 * @package    Mage_Directory
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Country collection
 *
 * @category   Mage
 * @package    Mage_Directory
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Directory_Model_Mysql4_Country_Collection extends Varien_Data_Collection_Db
{
    protected $_countryTable;

    public function __construct()
    {
        parent::__construct(Mage::getSingleton('core/resource')->getConnection('directory_read'));

        $this->_countryTable = Mage::getSingleton('core/resource')->getTableName('directory/country');

        $this->_select->from(array('country'=>$this->_countryTable));
        $this->setItemObjectClass(Mage::getConfig()->getModelClassName('directory/country'));
    }

    public function loadByStore()
    {
        $allowCountries = explode(',', (string)Mage::getStoreConfig('general/country/allow'));
        if (!empty($allowCountries)) {
            $this->addFieldToFilter("country.country_id", array('in'=>$allowCountries));
        }

        $this->load();

        return $this;
    }

    public function getItemById($countryId)
    {
        foreach ($this->_items as $country) {
            if ($country->getCountryId() == $countryId) {
                return $country;
            }
        }
        return Mage::getResourceModel('directory/country');
    }

    public function addCountryCodeFilter($countryCode, $iso=array(0 => 'iso3', 'iso2'))
    {
        if (!empty($countryCode)) {
            $where_expr = '';
            if (is_array($countryCode)) {
                if (is_array($iso)) {
                    $i = 0;
                    foreach ($iso as $iso_curr) {
                        $where_expr .= ($i++ > 0 ? ' OR ' : '');
                        $where_expr .= "country.{$iso_curr}_code IN ('".implode("','", $countryCode)."')";
                    }
                } else {
                    $where_expr = "country.{$iso}_code IN ('".implode("','", $countryCode)."')";
                }
            } else {
                if (is_array($iso)) {
                    $i = 0;
                    foreach ($iso as $iso_curr) {
                        $where_expr .= ($i++ > 0 ? ' OR ' : '');
                        $where_expr = "country.{$iso_curr}_code = '{$countryCode}'";
                    }
                } else {
                    $where_expr = "country.{$iso}_code = '{$countryCode}'";
                }
            }
            $this->_select->where($where_expr);
        }
        return $this;
    }

    public function addCountryIdFilter($countryId)
    {
        if (!empty($countryId)) {
            if (is_array($countryId)) {
                $this->_select->where("country.country_id IN ('".implode("','", $countryId)."')");
            } else {
                $this->_select->where("country.country_id = '{$countryId}'");
            }
        }
        return $this;
    }

    public function toOptionArray($emptyLabel = '')
    {
        $options = $this->_toOptionArray('country_id', 'name', array('title'=>'iso2_code'));

        $sort = array();
        foreach ($options as $index=>$data) {
            $name = Mage::app()->getLocale()->getLocale()->getCountryTranslation($data['value']);
            if (!empty($name)) {
                $sort[$name] = $data['value'];
            }
        }

        ksort($sort);
        $options = array();
        foreach ($sort as $label=>$value) {
            $options[] = array(
               'value' => $value,
               'label' => $label
            );
        }

        if (count($options)>0 && $emptyLabel !== false) {
            array_unshift($options, array('value'=>'', 'label'=>$emptyLabel));
        }
        return $options;
    }
}
