<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Product attribute extension with event dispatching
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 *
 * @method Mage_Catalog_Model_Resource_Attribute _getResource()
 * @method Mage_Catalog_Model_Resource_Attribute getResource()
 * @method string getFrontendInputRenderer()
 * @method $this setFrontendInputRenderer(string $value)
 * @method int setIsGlobal(int $value)
 * @method int getIsVisible()
 * @method int setIsVisible(int $value)
 * @method int getIsSearchable()
 * @method $this setIsSearchable(int $value)
 * @method int getSearchWeight()
 * @method $this setSearchWeight(int $value)
 * @method int getIsFilterable()
 * @method $this setIsFilterable(int $value)
 * @method int getIsComparable()
 * @method $this setIsComparable(int $value)
 * @method $this setIsVisibleOnFront(int $value)
 * @method int getIsHtmlAllowedOnFront()
 * @method $this setIsHtmlAllowedOnFront(int $value)
 * @method int getIsUsedForPriceRules()
 * @method $this setIsUsedForPriceRules(int $value)
 * @method int getIsFilterableInSearch()
 * @method $this setIsFilterableInSearch(int $value)
 * @method int getUsedInProductListing()
 * @method $this setUsedInProductListing(int $value)
 * @method int getUsedForSortBy()
 * @method $this setUsedForSortBy(int $value)
 * @method int getIsConfigurable()
 * @method $this setIsConfigurable(int $value)
 * @method string getApplyTo()
 * @method $this setApplyTo(string $value)
 * @method int getIsVisibleInAdvancedSearch()
 * @method $this setIsVisibleInAdvancedSearch(int $value)
 * @method int getPosition()
 * @method $this setPosition(int $value)
 * @method int getIsWysiwygEnabled()
 * @method $this setIsWysiwygEnabled(int $value)
 * @method int getIsUsedForPromoRules()
 * @method $this setIsUsedForPromoRules(int $value)
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
