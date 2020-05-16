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
 * @package     Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Category tabs
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Catalog_Category_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    /**
     * Default Attribute Tab Block
     *
     * @var string
     */
    protected $_attributeTabBlock = 'adminhtml/catalog_category_tab_attributes';

    /**
     * Initialize Tabs
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('category_info_tabs');
        $this->setDestElementId('category_tab_content');
        $this->setTitle(Mage::helper('catalog')->__('Category Data'));
        $this->setTemplate('widget/tabshoriz.phtml');
    }

    /**
     * Retrieve cattegory object
     *
     * @return Mage_Catalog_Model_Category
     */
    public function getCategory()
    {
        return Mage::registry('current_category');
    }

    /**
     * Return Adminhtml Catalog Helper
     *
     * @return Mage_Adminhtml_Helper_Catalog
     */
    public function getCatalogHelper()
    {
        return Mage::helper('adminhtml/catalog');
    }

    /**
     * Getting attribute block name for tabs
     *
     * @return string
     */
    public function getAttributeTabBlock()
    {
        if ($block = $this->getCatalogHelper()->getCategoryAttributeTabBlock()) {
            return $block;
        }
        return $this->_attributeTabBlock;
    }

    /**
     * Prepare Layout Content
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $categoryAttributes = $this->getCategory()->getAttributes();
        if (!$this->getCategory()->getId()) {
            foreach ($categoryAttributes as $attribute) {
                $default = $attribute->getDefaultValue();
                if ($default != '') {
                    $this->getCategory()->setData($attribute->getAttributeCode(), $default);
                }
            }
        }

        $attributeSetId     = $this->getCategory()->getDefaultAttributeSetId();
        /** @var $groupCollection Mage_Eav_Model_Resource_Entity_Attribute_Group_Collection */
        $groupCollection    = Mage::getResourceModel('eav/entity_attribute_group_collection')
            ->setAttributeSetFilter($attributeSetId)
            ->setSortOrder()
            ->load();
        $defaultGroupId = 0;
        foreach ($groupCollection as $group) {
            /* @var $group Mage_Eav_Model_Entity_Attribute_Group */
            if ($defaultGroupId == 0 or $group->getIsDefault()) {
                $defaultGroupId = $group->getId();
            }
        }

        foreach ($groupCollection as $group) {
            /* @var $group Mage_Eav_Model_Entity_Attribute_Group */
            $attributes = array();
            foreach ($categoryAttributes as $attribute) {
                /* @var $attribute Mage_Eav_Model_Entity_Attribute */
                if ($attribute->isInGroup($attributeSetId, $group->getId())) {
                    $attributes[] = $attribute;
                }
            }

            // do not add grops without attributes
            if (!$attributes) {
                continue;
            }

            $active  = $defaultGroupId == $group->getId();
            $block = $this->getLayout()->createBlock($this->getAttributeTabBlock(), '')
                ->setGroup($group)
                ->setAttributes($attributes)
                ->setAddHiddenFields($active)
                ->toHtml();
            $this->addTab('group_' . $group->getId(), array(
                'label'     => Mage::helper('catalog')->__($group->getAttributeGroupName()),
                'content'   => $block,
                'active'    => $active
            ));
        }

        $this->addTab('products', array(
            'label'     => Mage::helper('catalog')->__('Category Products'),
            'content'   => $this->getLayout()->createBlock(
                'adminhtml/catalog_category_tab_product',
                'category.product.grid'
            )->toHtml(),
        ));

        // dispatch event add custom tabs
        Mage::dispatchEvent('adminhtml_catalog_category_tabs', array(
            'tabs'  => $this
        ));

        /*$this->addTab('features', array(
            'label'     => Mage::helper('catalog')->__('Feature Products'),
            'content'   => 'Feature Products'
        ));        */
        return parent::_prepareLayout();
    }
}
