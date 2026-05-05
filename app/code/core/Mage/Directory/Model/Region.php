<?php

declare(strict_types=1);

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Directory
 */

/**
 * @package    Mage_Directory
 *
 * @method Mage_Directory_Model_Resource_Region            _getResource()
 * @method Mage_Directory_Model_Resource_Region_Collection getCollection()
 * @method string                                          getDefaultName()
 * @method int                                             getRegionId()
 * @method Mage_Directory_Model_Resource_Region            getResource()
 * @method Mage_Directory_Model_Resource_Region_Collection getResourceCollection()
 * @method $this                                           setDefaultName(string $value)
 */
class Mage_Directory_Model_Region extends Mage_Core_Model_Abstract
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('directory/region');
    }

    public function getCode(): string
    {
        return (string) $this->_getData('code');
    }

    public function getCountryId(): string
    {
        return (string) $this->_getData('country_id');
    }

    public function setCode(string $value): static
    {
        return $this->setData('code', $value);
    }

    public function setCountryId(string $value): static
    {
        return $this->setData('country_id', $value);
    }

    /**
     * Retrieve region name
     *
     * If name is no declared, then default_name is used
     *
     * @return string
     */
    public function getName()
    {
        $name = $this->getDataByKey('name');
        if (is_null($name)) {
            return $this->getDataByKey('default_name');
        }

        return $name;
    }

    /**
     * @param  string $code
     * @param  string $countryId
     * @return $this
     */
    public function loadByCode($code, $countryId)
    {
        if ($code) {
            $this->_getResource()->loadByCode($this, $code, $countryId);
        }

        return $this;
    }

    /**
     * @param  string $name
     * @param  string $countryId
     * @return $this
     */
    public function loadByName($name, $countryId)
    {
        $this->_getResource()->loadByName($this, $name, $countryId);
        return $this;
    }
}
