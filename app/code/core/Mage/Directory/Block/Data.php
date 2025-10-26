<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Directory
 */

/**
 * Directory data block
 *
 * @package    Mage_Directory
 * @phpstan-type Option array{label: string, value: non-falsy-string}
 *
 * @method int getRegionId()
 */
class Mage_Directory_Block_Data extends Mage_Core_Block_Template
{
    /**
     * @codeCoverageIgnore
     * @return string
     * @deprecated
     */
    public function getLoadrRegionUrl()
    {
        return $this->getUrl('directory/json/childRegion');
    }

    /**
     * @return Mage_Directory_Model_Resource_Country_Collection
     */
    public function getCountryCollection()
    {
        $collection = $this->getData('country_collection');
        if (is_null($collection)) {
            $collection = Mage::getModel('directory/country')->getResourceCollection()
                ->loadByStore();
            $this->setData('country_collection', $collection);
        }

        return $collection;
    }

    /**
     * @param string|null $defValue
     * @param string $name
     * @param string $id
     * @param string $title
     * @return string
     * @throws Mage_Core_Model_Store_Exception
     */
    public function getCountryHtmlSelect($defValue = null, $name = 'country_id', $id = 'country', $title = 'Country')
    {
        Varien_Profiler::start('TEST: ' . __METHOD__);
        if (is_null($defValue)) {
            $defValue = $this->getCountryId();
        }

        $cacheKey = 'DIRECTORY_COUNTRY_SELECT_STORE_' . Mage::app()->getStore()->getCode();
        if (Mage::app()->useCache('config') && $cache = Mage::app()->loadCache($cacheKey)) {
            $options = unserialize($cache, ['allowed_classes' => false]);
        } else {
            $options = $this->getCountryCollection()->toOptionArray();
            if (Mage::app()->useCache('config')) {
                Mage::app()->saveCache(serialize($options), $cacheKey, ['config']);
            }
        }

        $html = $this->getLayout()->createBlock('core/html_select')
            ->setName($name)
            ->setId($id)
            ->setTitle(Mage::helper('directory')->__($title))
            ->setClass('validate-select')
            ->setValue($defValue)
            ->setOptions($this->sortCountryOptions($options))
            ->getHtml();

        Varien_Profiler::stop('TEST: ' . __METHOD__);
        return $html;
    }

    /**
     * @return Mage_Directory_Model_Resource_Region_Collection
     */
    public function getRegionCollection()
    {
        $collection = $this->getData('region_collection');
        if (is_null($collection)) {
            $collection = Mage::getModel('directory/region')->getResourceCollection()
                ->addCountryFilter($this->getCountryId())
                ->load();

            $this->setData('region_collection', $collection);
        }

        return $collection;
    }

    /**
     * @return string
     * @throws Mage_Core_Model_Store_Exception
     */
    public function getRegionHtmlSelect()
    {
        Varien_Profiler::start('TEST: ' . __METHOD__);
        $cacheKey = 'DIRECTORY_REGION_SELECT_STORE' . Mage::app()->getStore()->getId();
        if (Mage::app()->useCache('config') && $cache = Mage::app()->loadCache($cacheKey)) {
            $options = unserialize($cache, ['allowed_classes' => false]);
        } else {
            $options = $this->getRegionCollection()->toOptionArray();
            if (Mage::app()->useCache('config')) {
                Mage::app()->saveCache(serialize($options), $cacheKey, ['config']);
            }
        }

        $html = $this->getLayout()->createBlock('core/html_select')
            ->setName('region')
            ->setTitle(Mage::helper('directory')->__('State/Province'))
            ->setId('state')
            ->setClass('required-entry validate-state')
            ->setValue((int) $this->getRegionId())
            ->setOptions($options)
            ->getHtml();
        Varien_Profiler::start('TEST: ' . __METHOD__);
        return $html;
    }

    /**
     * @return string
     */
    public function getCountryId()
    {
        $countryId = $this->getData('country_id');
        if (is_null($countryId)) {
            $countryId = Mage::helper('core')->getDefaultCountry();
        }

        return $countryId;
    }

    /**
     * @return string
     */
    public function getRegionsJs()
    {
        Varien_Profiler::start('TEST: ' . __METHOD__);
        $regionsJs = $this->getData('regions_js');
        if (!$regionsJs) {
            $countryIds = [];
            foreach ($this->getCountryCollection() as $country) {
                $countryIds[] = $country->getCountryId();
            }

            $collection = Mage::getModel('directory/region')->getResourceCollection()
                ->addCountryFilter($countryIds)
                ->load();
            $regions = [];
            /** @var Mage_Directory_Model_Region $region */
            foreach ($collection as $region) {
                if (!$region->getRegionId()) {
                    continue;
                }

                $regions[$region->getCountryId()][$region->getRegionId()] = [
                    'code' => $region->getCode(),
                    'name' => $region->getName(),
                ];
            }

            $regionsJs = Mage::helper('core')->jsonEncode($regions);
        }

        Varien_Profiler::stop('TEST: ' . __METHOD__);
        return $regionsJs;
    }

    /**
     * @template T of Option[]
     * @param T $countryOptions
     * @return array{0: array{label: string, value: Option[]}, 1: array{label: string, value: Option[]}}|T
     */
    private function sortCountryOptions(array $countryOptions): array
    {
        $topCountryCodes = $this->helper('directory')->getTopCountryCodes();
        $headOptions = $tailOptions = [];

        foreach ($countryOptions as $countryOption) {
            if (in_array($countryOption['value'], $topCountryCodes)) {
                $headOptions[] = $countryOption;
            } else {
                $tailOptions[] = $countryOption;
            }
        }

        if (empty($headOptions)) {
            return $countryOptions;
        }

        return [['label' => $this->__('Popular'), 'value' => $headOptions], ['label' => $this->__('Others'), 'value' => $tailOptions]];
    }
}
