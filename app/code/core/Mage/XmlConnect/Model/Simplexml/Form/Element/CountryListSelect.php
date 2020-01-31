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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Xmlconnect form country list select element
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Model_Simplexml_Form_Element_CountryListSelect
    extends Mage_XmlConnect_Model_Simplexml_Form_Element_Select
{
    /**
     * Country list values array
     *
     * @var array
     */
    protected  $_countryListValues = array('country_id', 'region_id', 'region');

    /**
     * Init country list select element
     *
     * @param array $attributes
     */
    public function __construct($attributes = array())
    {
        parent::__construct($attributes);
    }

    /**
     * Set country list values 'country_id' and 'region_id'
     *
     * @return $this
     */
    protected function _setValues()
    {
        $value = $this->getValue();

        foreach ($this->_countryListValues as $param) {
            if (!isset($value[$param])) {
                continue;
            }
            $this->setData($param, $value[$param]);
        }

        return $this;
    }

    /**
     * Get values using old standard
     *
     * @deprecated old output standard
     * @param Mage_XmlConnect_Model_Simplexml_Element $xmlObj
     * @return $this
     */
    protected function _addOldStandardValue(Mage_XmlConnect_Model_Simplexml_Element $xmlObj)
    {
        $countries = $this->_getCountryOptions();

        if (is_array($countries)) {
            $valuesXmlObj = $xmlObj->addCustomChild('values');
            foreach ($countries as $data) {
                $regions = array();

                if ($data['value']) {
                    $regions = $this->_getRegionOptions($data['value']);
                }

                $relationType = is_array($regions) && !empty($regions) ? 'region_id' : 'region';

                $selectedCountry = array();
                if ($this->getCountryId() == $data['value']) {
                    $selectedCountry = array('selected' => 1);
                }

                $item = $valuesXmlObj->addCustomChild('item', null, array(
                    'relation' => $relationType
                ) + $selectedCountry);

                $item->addCustomChild('label', (string)$data['label']);
                $item->addCustomChild('value', $data['value']);

                if ($relationType == 'region_id') {
                    $regionsXmlObj = $item->addCustomChild('regions');
                    foreach ($regions as $regionData) {
                        $selectedRegion = array();

                        if (!empty($selectedCountry) && $this->getRegionId() == $regionData['value']) {
                            $selectedRegion = array('selected' => 1);
                        }

                        $regionItem = $regionsXmlObj->addCustomChild('region_item', null, $selectedRegion);
                        $regionItem->addCustomChild('label', (string)$regionData['label']);
                        $regionItem->addCustomChild('value', (string)$regionData['value']);
                    }
                }
            }
        }

        return $this;
    }

    /**
     * Add value to element
     *
     * @param Mage_XmlConnect_Model_Simplexml_Element $xmlObj
     * @return Mage_XmlConnect_Model_Simplexml_Form_Element_Abstract
     */
    protected function _addValue(Mage_XmlConnect_Model_Simplexml_Element $xmlObj)
    {
        $this->_setValues();
        $form = $this->getForm();

        if ($this->getOldFormat()) {
            $this->_addOldStandardValue($xmlObj);
            return $this;
        }

        if ($this->getCountryId()) {
            $xmlObj->addAttribute('value', $xmlObj->xmlAttribute($this->getCountryId()));
        }
        $countries = $this->_getCountryOptions();

        if (is_array($countries)) {
            $values = $xmlObj->addCustomChild('values');
            foreach ($countries as $data) {
                $regions = array();
                $countryValueAttribute = '';
                if ($data['value']) {
                    $regions = $this->_getRegionOptions($data['value']);
                }

                if (is_array($regions) && !empty($regions)) {
                    $relationType = 'region_id';
                    $countryValueAttribute = $data['value'];
                } else {
                    $relationType = 'region';
                }

                $countryData = array(
                    'relation'  => $relationType,
                    'label'     => (string)$data['label']
                );

                if ($countryValueAttribute) {
                    $countryData['value'] = $countryValueAttribute;
                }

                $item = $values->addCustomChild('item', $data['value'], $countryData);

                if ($relationType !== 'region') {

                    $selectedRegion = array();
                    if ($this->getCountryId() == $data['value']) {
                        $selectedRegion = array('value' => $this->getRegionId());
                    }

                    $suffix = $form->getFieldNameSuffix();
                    $regionFieldName = 'region_id';
                    if ($suffix) {
                        $regionFieldName = $form->addSuffixToName($regionFieldName, $suffix);
                    }

                    $regionsXmlObj = $item->addCustomChild('field', null, array(
                            'id'    => 'region_id_' . $data['value'],
                            'name'  => $regionFieldName,
                            'label' => Mage::helper('xmlconnect')->__('State/Province'),
                            'type'  => 'select',
                            'required' => 1
                        ) + $selectedRegion
                    );

                    $regionValues = $regionsXmlObj->addCustomChild('values');

                    foreach ($regions as $regionData) {
                        // Skip "Please select" item
                        if ($regionData['value'] == 0) {
                            continue;
                        }
                        $regionValues->addCustomChild('item', (string)$regionData['value'], array(
                            'label' => (string)$regionData['label']
                        ));
                    }
                } elseif ($this->getCountryId() == $data['value']) {
                    $item->addAttribute('value', $data['value']);
                    $item->addCustomChild('field', null, array(
                        'id'    => 'region_' . $data['value'],
                        'name'  => 'region',
                        'label' => Mage::helper('xmlconnect')->__('State/Province'),
                        'type'  => 'text',
                        'value' => $this->getRegion(),
                        'required' => 1
                    ));
                }
            }
        }
        return $this;
    }

    /**
     * Retrieve regions by country
     *
     * @param string $countryId
     * @return array
     */
    protected function _getRegionOptions($countryId)
    {
        $cacheKey = 'DIRECTORY_REGION_SELECT_STORE' . Mage::app()->getStore()->getId() . $countryId;
        $cache = Mage::app()->loadCache($cacheKey);
        if (Mage::app()->useCache('config') && $cache) {
            $options = unserialize($cache);
        } else {
            $collection = Mage::getModel('directory/region')->getResourceCollection()->addCountryFilter($countryId)
                ->load();
            $options = $collection->toOptionArray();
            if (Mage::app()->useCache('config')) {
                Mage::app()->saveCache(serialize($options), $cacheKey, array('config'));
            }
        }
        return $options;
    }

    /**
     * Retrieve countries
     *
     * @return array
     */
    protected function _getCountryOptions()
    {
        $cacheKey = 'DIRECTORY_COUNTRY_SELECT_STORE_' . Mage::app()->getStore()->getCode();
        $cache = Mage::app()->loadCache($cacheKey);
        if (Mage::app()->useCache('config') && $cache) {
            $options = unserialize($cache);
        } else {
            $collection = Mage::getModel('directory/country')->getResourceCollection()->loadByStore();
            $options = $collection->toOptionArray(false);
            if (Mage::app()->useCache('config')) {
                Mage::app()->saveCache(serialize($options), $cacheKey, array('config'));
            }
        }
        return $options;
    }
}
