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
 * Country collection
 *
 * @category   Mage
 * @package    Mage_Directory
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Directory_Model_Mysql4_Region_Collection extends Varien_Data_Collection_Db
{
    protected $_regionTable;
    protected $_regionNameTable;
    protected $_countryTable;

    public function __construct()
    {
        parent::__construct(Mage::getSingleton('core/resource')->getConnection('directory_read'));

        $this->_countryTable    = Mage::getSingleton('core/resource')->getTableName('directory/country');
        $this->_regionTable     = Mage::getSingleton('core/resource')->getTableName('directory/country_region');
        $this->_regionNameTable = Mage::getSingleton('core/resource')->getTableName('directory/country_region_name');

        $locale =  $this->_select->getAdapter()->quote(
            Mage::app()->getLocale()->getLocaleCode());


        $this->_select->from(array('region'=>$this->_regionTable),
            array(
                 'region_id'    => 'region_id',
                 'country_id'   => 'country_id',
                 'code'         => 'code',
                 'default_name' => 'default_name'
            ));
        $this->_select->joinLeft(array('rname'=>$this->_regionNameTable),
            'region.region_id = rname.region_id AND rname.locale = ' . $locale,
            array('name'));

        $this->setItemObjectClass(Mage::getConfig()->getModelClassName('directory/region'));
    }

    public function addCountryFilter($countryId)
    {
        if (!empty($countryId)) {
            if (is_array($countryId)) {
                $this->addFieldToFilter('region.country_id', array('in' => $countryId));
            } else {
                $this->addFieldToFilter('region.country_id', $countryId);
            }
        }
        return $this;
    }

    public function addCountryCodeFilter($countryCode)
    {
        $this->_select->joinLeft(array('country'=>$this->_countryTable), 'region.country_id=country.country_id');
        $this->_select->where('country.iso3_code = ?', $countryCode);
        return $this;
    }

    public function addRegionCodeFilter($regionCode)
    {
        if (!empty($regionCode)) {
            if (is_array($regionCode)) {
                $this->_select->where('region.code IN(?)', $regionCode);
            } else {
                $this->_select->where('region.code = ?', $regionCode);
            }
        }
        return $this;
    }

    public function addRegionNameFilter($regionName)
    {
        if (!empty($regionName)) {
            if (is_array($regionName)) {
                $this->_select->where('region.default_name IN(?)', $regionName);
            } else {
                $this->_select->where('region.default_name = ?', $regionName);
            }
        }
        return $this;
    }

    public function toOptionArray()
    {
        $options = array();
        foreach ($this as $item) {
            $options[] = array(
               'value' => $item->getId(),
               'label' => $item->getName()
            );
        }
        if (count($options)>0) {
            array_unshift($options,
                array('title' => null,
                     'value' => '0',
                     'label' => Mage::helper('directory')->__('-- Please select --')));
        }
        return $options;
    }
}
