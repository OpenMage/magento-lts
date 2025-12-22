<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Category chooser for Wysiwyg CMS widget
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Catalog_Category_Widget_Chooser extends Mage_Adminhtml_Block_Catalog_Category_Tree
{
    protected $_selectedCategories = [];

    /**
     * Block construction
     * Defines tree template and init tree params
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('catalog/category/widget/tree.phtml');
        $this->_withProductCount = false;
    }

    /**
     * Setter
     *
     * @param  array $selectedCategories
     * @return $this
     */
    public function setSelectedCategories($selectedCategories)
    {
        $this->_selectedCategories = $selectedCategories;
        return $this;
    }

    /**
     * Getter
     *
     * @return array
     */
    public function getSelectedCategories()
    {
        return $this->_selectedCategories;
    }

    /**
     * Prepare chooser element HTML
     *
     * @param  Varien_Data_Form_Element_Abstract $element Form Element
     * @return Varien_Data_Form_Element_Abstract
     */
    public function prepareElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $uniqId = Mage::helper('core')->uniqHash($element->getId());
        $sourceUrl = $this->getUrl(
            '*/catalog_category_widget/chooser',
            ['uniq_id' => $uniqId, 'use_massaction' => false],
        );

        $chooser = $this->getLayout()->createBlock('widget/adminhtml_widget_chooser')
            ->setElement($element)
            ->setTranslationHelper($this->getTranslationHelper())
            ->setConfig($this->getConfig())
            ->setFieldsetId($this->getFieldsetId())
            ->setSourceUrl($sourceUrl)
            ->setUniqId($uniqId);

        if ($element->getValue()) {
            $value = explode('/', $element->getValue());
            $categoryId = false;
            if (isset($value[0]) && isset($value[1]) && $value[0] == 'category') {
                $categoryId = $value[1];
            }

            if ($categoryId) {
                $label = $this->_getModelAttributeByEntityId('catalog/category', 'name', $categoryId);
                $chooser->setLabel($label);
            }
        }

        $element->setData('after_element_html', $chooser->toHtml());
        return $element;
    }

    /**
     * Retrieve model attribute value
     *
     * @param  string $modelType     Model Type
     * @param  string $attributeName Attribute Name
     * @param  string $entityId      Form Entity ID
     * @return string
     */
    protected function _getModelAttributeByEntityId($modelType, $attributeName, $entityId)
    {
        $result = '';
        $model = Mage::getModel($modelType)
            ->getCollection()
            ->addAttributeToSelect($attributeName)
            ->addAttributeToFilter('entity_id', $entityId)
            ->getFirstItem();
        if ($model) {
            return $model->getData($attributeName);
        }

        return $result;
    }

    /**
     * Category Tree node onClick listener js function
     *
     * @return string
     */
    public function getNodeClickListener()
    {
        if ($this->getData('node_click_listener')) {
            return $this->getData('node_click_listener');
        }

        if ($this->getUseMassaction()) {
            $js = '
                function (node, e) {
                    if (node.ui.toggleCheck) {
                        node.ui.toggleCheck(true);
                    }
                }
            ';
        } else {
            $chooserJsObject = $this->getId();
            $js = '
                function (node, e) {
                    ' . $chooserJsObject . '.setElementValue("category/" + node.attributes.id);
                    ' . $chooserJsObject . '.setElementLabel(node.text);
                    ' . $chooserJsObject . '.close();
                }
            ';
        }

        return $js;
    }

    /**
     * Get JSON of a tree node or an associative array
     *
     * @param  array|Varien_Data_Tree_Node $node
     * @param  int                         $level
     * @return array
     */
    protected function _getNodeJson($node, $level = 0)
    {
        $item = parent::_getNodeJson($node, $level);
        if (in_array($node->getId(), $this->getSelectedCategories())) {
            $item['checked'] = true;
        }

        $item['is_anchor'] = (int) $node->getIsAnchor();
        $item['url_key'] = $node->getData('url_key');
        return $item;
    }

    /**
     * Adds some extra params to categories collection
     *
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Collection
     */
    public function getCategoryCollection()
    {
        return parent::getCategoryCollection()->addAttributeToSelect('url_key')->addAttributeToSelect('is_anchor');
    }

    /**
     * Tree JSON source URL
     *
     * @return string
     */
    public function getLoadTreeUrl($expanded = null)
    {
        return $this->getUrl('*/catalog_category_widget/categoriesJson', [
            '_current' => true,
            'uniq_id' => $this->getId(),
            'use_massaction' => $this->getUseMassaction(),
        ]);
    }
}
