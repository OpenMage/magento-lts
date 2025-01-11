<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Directory
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Country model
 *
 * @category   Mage
 * @package    Mage_Directory
 *
 * @method Mage_Directory_Model_Resource_Country _getResource()
 * @method Mage_Directory_Model_Resource_Country getResource()
 * @method Mage_Directory_Model_Resource_Country_Collection getResourceCollection()
 * @method string getCode()
 * @method string getCountryId()
 * @method $this setCountryId(string $value)
 * @method string getIso2Code()
 * @method $this setIso2Code(string $value)
 * @method string getIso3Code()
 * @method $this setIso3Code(string $value)
 */
class Mage_Directory_Model_Country extends Mage_Core_Model_Abstract
{
    public static $_format = [];

    protected function _construct()
    {
        $this->_init('directory/country');
    }

    /**
     * @param string $code
     * @return $this
     * @throws Mage_Core_Exception
     */
    public function loadByCode($code)
    {
        $this->_getResource()->loadByCode($this, $code);
        return $this;
    }

    /**
     * @return Mage_Directory_Model_Resource_Region_Collection
     */
    public function getRegions()
    {
        return $this->getLoadedRegionCollection();
    }

    /**
     * @return Mage_Directory_Model_Resource_Region_Collection
     */
    public function getLoadedRegionCollection()
    {
        $collection = $this->getRegionCollection();
        $collection->load();
        return $collection;
    }

    /**
     * @return Mage_Directory_Model_Resource_Region_Collection
     */
    public function getRegionCollection()
    {
        $collection = Mage::getResourceModel('directory/region_collection');
        $collection->addCountryFilter($this->getId());
        return $collection;
    }

    /**
     * @param bool $html
     * @return string
     */
    public function formatAddress(Varien_Object $address, $html = false)
    {
        //TODO: is it still used?
        $address->getRegion();
        $address->getCountry();

        $template = $this->getData('address_template_' . ($html ? 'html' : 'plain'));
        if (empty($template)) {
            if (!$this->getId()) {
                $template = '{{firstname}} {{lastname}}';
            } elseif (!$html) {
                $template = '{{firstname}} {{lastname}}
{{company}}
{{street1}}
{{street2}}
{{city}}, {{region}} {{postcode}}';
            } else {
                $template = '{{firstname}} {{lastname}}<br/>
{{street}}<br/>
{{city}}, {{region}} {{postcode}}<br/>
T: {{telephone}}';
            }
        }

        $filter = new Varien_Filter_Template_Simple();
        $addressText = $filter->setData($address->getData())->filter($template);

        if ($html) {
            $addressText = preg_replace('#(<br\s*/?>\s*){2,}#im', '<br/>', $addressText);
        } else {
            $addressText = preg_replace('#(\n\s*){2,}#m', "\n", $addressText);
        }

        return $addressText;
    }

    /**
     * Retrieve formats for
     *
     * @return Mage_Directory_Model_Resource_Country_Format_Collection
     */
    public function getFormats()
    {
        if (!isset(self::$_format[$this->getId()]) && $this->getId()) {
            self::$_format[$this->getId()] = Mage::getModel('directory/country_format')
                ->getCollection()
                ->setCountryFilter($this)
                ->load();
        }

        return self::$_format[$this->getId()] ?? null;
    }

    /**
     * Retrieve format
     *
     * @param string $type
     * @return Mage_Directory_Model_Country_Format
     */
    public function getFormat($type)
    {
        if ($this->getFormats()) {
            foreach ($this->getFormats() as $format) {
                if ($format->getType() == $type) {
                    return $format;
                }
            }
        }
        return null;
    }

    /**
     * @return string
     */
    public function getName()
    {
        if (!$this->getData('name')) {
            $this->setData(
                'name',
                Mage::app()->getLocale()->getCountryTranslation($this->getId()),
            );
        }
        return $this->getData('name');
    }
}
