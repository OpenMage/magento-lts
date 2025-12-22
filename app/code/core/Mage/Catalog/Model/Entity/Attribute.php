<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/**
 * Product attribute extension with event dispatching
 *
 * @package    Mage_Catalog
 *
 * @method Mage_Catalog_Model_Resource_Attribute _getResource()
 * @method string                                getFrontendInputRenderer()
 * @method int                                   getIsComparable()
 * @method int                                   getIsConfigurable()
 * @method int                                   getIsFilterable()
 * @method int                                   getIsFilterableInSearch()
 * @method int                                   getIsHtmlAllowedOnFront()
 * @method int                                   getIsSearchable()
 * @method int                                   getIsUsedForPriceRules()
 * @method int                                   getIsUsedForPromoRules()
 * @method int                                   getIsVisible()
 * @method int                                   getIsVisibleInAdvancedSearch()
 * @method int                                   getIsWysiwygEnabled()
 * @method int                                   getPosition()
 * @method Mage_Catalog_Model_Resource_Attribute getResource()
 * @method int                                   getSearchWeight()
 * @method int                                   getUsedForSortBy()
 * @method int                                   getUsedInProductListing()
 * @method $this                                 setApplyTo(array|string $value)
 * @method $this                                 setFrontendInputRenderer(string $value)
 * @method $this                                 setIsComparable(int $value)
 * @method $this                                 setIsConfigurable(int $value)
 * @method $this                                 setIsFilterable(int $value)
 * @method $this                                 setIsFilterableInSearch(int $value)
 * @method int                                   setIsGlobal(int $value)
 * @method $this                                 setIsHtmlAllowedOnFront(int $value)
 * @method $this                                 setIsSearchable(int $value)
 * @method $this                                 setIsUsedForPriceRules(int $value)
 * @method $this                                 setIsUsedForPromoRules(int $value)
 * @method int                                   setIsVisible(int $value)
 * @method $this                                 setIsVisibleInAdvancedSearch(int $value)
 * @method $this                                 setIsVisibleOnFront(int $value)
 * @method $this                                 setIsWysiwygEnabled(int $value)
 * @method $this                                 setPosition(int $value)
 * @method $this                                 setSearchWeight(int $value)
 * @method $this                                 setUsedForSortBy(int $value)
 * @method $this                                 setUsedInProductListing(int $value)
 */
class Mage_Catalog_Model_Entity_Attribute extends Mage_Eav_Model_Entity_Attribute
{
    protected $_eventPrefix = 'catalog_entity_attribute';

    protected $_eventObject = 'attribute';

    public const MODULE_NAME = 'Mage_Catalog';

    /**
     * Processing object before save data
     *
     * @return Mage_Core_Model_Abstract
     */
    protected function _beforeSave()
    {
        if ($this->_getResource()->isUsedBySuperProducts($this)) {
            throw Mage::exception('Mage_Eav', Mage::helper('eav')->__('This attribute is used in configurable products'));
        }

        $this->setData('modulePrefix', self::MODULE_NAME);
        return parent::_beforeSave();
    }

    /**
     * Processing object after save data
     *
     * @return Mage_Core_Model_Abstract
     */
    protected function _afterSave()
    {
        /**
         * Fix saving attribute in admin
         */
        Mage::getSingleton('eav/config')->clear();
        return parent::_afterSave();
    }
}
