<?php

declare(strict_types=1);

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Directory
 */

/**
 * Country model
 *
 * @package    Mage_Directory
 *
 * @method Mage_Directory_Model_Resource_Country            _getResource()
 * @method string                                           getCode()
 * @method Mage_Directory_Model_Resource_Country_Collection getCollection()
 * @method Mage_Directory_Model_Resource_Country            getResource()
 * @method Mage_Directory_Model_Resource_Country_Collection getResourceCollection()
 */
class Mage_Directory_Model_Country extends Mage_Core_Model_Abstract
{
    public static $_format = [];

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('directory/country');
    }

    public function getCountryId(): string
    {
        return (string) $this->_getData('country_id');
    }

    public function getIso2Code(): ?string
    {
        $v = $this->_getData('iso2_code');
        return $v !== null ? (string) $v : null;
    }

    public function getIso3Code(): ?string
    {
        $v = $this->_getData('iso3_code');
        return $v !== null ? (string) $v : null;
    }

    public function setCountryId(string $value): static
    {
        return $this->setData('country_id', $value);
    }

    public function setIso2Code(?string $value): static
    {
        return $this->setData('iso2_code', $value);
    }

    public function setIso3Code(?string $value): static
    {
        return $this->setData('iso3_code', $value);
    }

    /**
     * @param  string              $code
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
     * @param  bool   $html
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
            return preg_replace('#(<br\s*/?>\s*){2,}#im', '<br/>', $addressText);
        }

        return preg_replace('#(\n\s*){2,}#m', "\n", $addressText);
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
     * @param  string                                   $type
     * @return null|Mage_Directory_Model_Country_Format
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
        if (!$this->getDataByKey('name')) {
            $this->setData(
                'name',
                Mage::app()->getLocale()->getCountryTranslation($this->getId()),
            );
        }

        return $this->getDataByKey('name');
    }
}
