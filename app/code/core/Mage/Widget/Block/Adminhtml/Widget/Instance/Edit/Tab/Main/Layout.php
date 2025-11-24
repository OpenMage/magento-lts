<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Widget
 */

/**
 * Widget Instance page groups (predefined layouts group) to display on
 *
 * @package    Mage_Widget
 *
 * @method Mage_Widget_Model_Widget_Instance getWidgetInstance()
 * @method $this setWidgetInstance(Mage_Widget_Model_Widget_Instance $value)
 */
class Mage_Widget_Block_Adminhtml_Widget_Instance_Edit_Tab_Main_Layout extends Mage_Adminhtml_Block_Template implements Varien_Data_Form_Element_Renderer_Interface
{
    /**
     * @var Varien_Data_Form_Element_Abstract
     */
    protected $_element = null;

    /**
     * Internal constructor
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('widget/instance/edit/layout.phtml');
    }

    /**
     * Render given element (return html of element)
     *
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $this->setElement($element);
        return $this->toHtml();
    }

    /**
     * Setter
     *
     * @return $this
     */
    public function setElement(Varien_Data_Form_Element_Abstract $element)
    {
        $this->_element = $element;
        return $this;
    }

    /**
     * Getter
     *
     * @return Varien_Data_Form_Element_Abstract
     */
    public function getElement()
    {
        return $this->_element;
    }

    /**
     * Generate url to get categories chooser by ajax query
     *
     * @return string
     */
    public function getCategoriesChooserUrl()
    {
        return $this->getUrl('*/*/categories', ['_current' => true]);
    }

    /**
     * Generate url to get products chooser by ajax query
     *
     * @return string
     */
    public function getProductsChooserUrl()
    {
        return $this->getUrl('*/*/products', ['_current' => true]);
    }

    /**
     * Generate url to get reference block chooser by ajax query
     *
     * @return string
     */
    public function getBlockChooserUrl()
    {
        return $this->getUrl('*/*/blocks', ['_current' => true]);
    }

    /**
     * Generate url to get template chooser by ajax query
     *
     * @return string
     */
    public function getTemplateChooserUrl()
    {
        return $this->getUrl('*/*/template', ['_current' => true]);
    }

    /**
     * Create and return html of select box Display On
     *
     * @return string
     */
    public function getDisplayOnSelectHtml()
    {
        $selectBlock = $this->getLayout()->createBlock('core/html_select')
            ->setName('widget_instance[{{id}}][page_group]')
            ->setId('widget_instance[{{id}}][page_group]')
            ->setClass('required-entry page_group_select select')
            ->setExtraParams("onchange=\"WidgetInstance.displayPageGroup(this.value+\'_{{id}}\')\"")
            ->setOptions($this->_getDisplayOnOptions());
        return $selectBlock->toHtml();
    }

    /**
     * Retrieve Display On options array.
     * - Categories (anchor and not anchor)
     * - Products (product types depend on configuration)
     * - Generic (predefined) pages (all pages and single layout update)
     *
     * @return array
     */
    protected function _getDisplayOnOptions()
    {
        $options = [];
        $options[] = [
            'value' => '',
            'label' => $this->helper('core')->jsQuoteEscape(Mage::helper('widget')->__('-- Please Select --')),
        ];
        $options[] = [
            'label' => Mage::helper('widget')->__('Categories'),
            'value' => [
                [
                    'value' => 'anchor_categories',
                    'label' => $this->helper('core')->jsQuoteEscape(Mage::helper('widget')->__('Anchor Categories')),
                ],
                [
                    'value' => 'notanchor_categories',
                    'label' => $this->helper('core')->jsQuoteEscape(Mage::helper('widget')->__('Non-Anchor Categories')),
                ],
            ],
        ];
        $productsOptions = [];
        foreach (Mage_Catalog_Model_Product_Type::getTypes() as $typeId => $type) {
            $productsOptions[] = [
                'value' => $typeId . '_products',
                'label' => $this->helper('core')->jsQuoteEscape($type['label']),
            ];
        }

        array_unshift($productsOptions, [
            'value' => 'all_products',
            'label' => $this->helper('core')->jsQuoteEscape(Mage::helper('widget')->__('All Product Types')),
        ]);
        $options[] = [
            'label' => $this->helper('core')->jsQuoteEscape(Mage::helper('widget')->__('Products')),
            'value' => $productsOptions,
        ];
        $options[] = [
            'label' => $this->helper('core')->jsQuoteEscape(Mage::helper('widget')->__('Generic Pages')),
            'value' => [
                [
                    'value' => 'all_pages',
                    'label' => $this->helper('core')->jsQuoteEscape(Mage::helper('widget')->__('All Pages')),
                ],
                [
                    'value' => 'pages',
                    'label' => $this->helper('core')->jsQuoteEscape(Mage::helper('widget')->__('Specified Page')),
                ],
            ],
        ];
        return $options;
    }

    /**
     * Generate array of parameters for every container type to create html template
     *
     * @return array
     */
    public function getDisplayOnContainers()
    {
        $container = [];
        $container['anchor'] = [
            'label' => 'Categories',
            'code' => 'categories',
            'name' => 'anchor_categories',
            'layout_handle' => 'default,catalog_category_layered',
            'is_anchor_only' => 1,
            'product_type_id' => '',
        ];
        $container['notanchor'] = [
            'label' => 'Categories',
            'code' => 'categories',
            'name' => 'notanchor_categories',
            'layout_handle' => 'default,catalog_category_default',
            'is_anchor_only' => 0,
            'product_type_id' => '',
        ];
        $container['all_products'] = [
            'label' => 'Products',
            'code' => 'products',
            'name' => 'all_products',
            'layout_handle' => 'default,catalog_product_view',
            'is_anchor_only' => '',
            'product_type_id' => '',
        ];
        foreach (Mage_Catalog_Model_Product_Type::getTypes() as $typeId => $type) {
            $container[$typeId] = [
                'label' => 'Products',
                'code' => 'products',
                'name' => $typeId . '_products',
                'layout_handle' => 'default,catalog_product_view,PRODUCT_TYPE_' . $typeId,
                'is_anchor_only' => '',
                'product_type_id' => $typeId,
            ];
        }

        return $container;
    }

    /**
     * Retrieve layout select chooser html
     *
     * @return string
     */
    public function getLayoutsChooser()
    {
        $layouts = $this->getLayout()
            ->createBlock('widget/adminhtml_widget_instance_edit_chooser_layout')
            ->setSelectName('widget_instance[{{id}}][pages][layout_handle]')
            ->setArea($this->getWidgetInstance()->getArea())
            ->setPackage($this->getWidgetInstance()->getPackage())
            ->setTheme($this->getWidgetInstance()->getTheme());
        return $layouts->toHtml();
    }

    /**
     * Retrieve add layout button html
     *
     * @return string
     */
    public function getAddLayoutButtonHtml()
    {
        $button = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData([
                'label'     => Mage::helper('widget')->__('Add Layout Update'),
                'onclick'   => 'WidgetInstance.addPageGroup({})',
                'class'     => 'add',
            ]);
        return $button->toHtml();
    }

    /**
     * Retrieve remove layout button html
     *
     * @return string
     */
    public function getRemoveLayoutButtonHtml()
    {
        $button = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData([
                'label'     => $this->helper('core')->jsQuoteEscape(Mage::helper('widget')->__('Remove Layout Update')),
                'onclick'   => 'WidgetInstance.removePageGroup(this)',
                'class'     => 'delete',
            ]);
        return $button->toHtml();
    }

    /**
     * Prepare and retrieve page groups data of widget instance
     *
     * @return array
     */
    public function getPageGroups()
    {
        $widgetInstance = $this->getWidgetInstance();
        $pageGroups = [];
        if ($widgetInstance->getPageGroups()) {
            foreach ($widgetInstance->getPageGroups() as $pageGroup) {
                $pageGroups[] = [
                    'page_id' => $pageGroup['page_id'],
                    'group' => $pageGroup['page_group'],
                    'block' => $pageGroup['block_reference'],
                    'for_value'   => $pageGroup['page_for'],
                    'layout_handle' => $pageGroup['layout_handle'],
                    $pageGroup['page_group'] . '_entities' => $pageGroup['entities'],
                    'template' => $pageGroup['page_template'],
                ];
            }
        }

        return $pageGroups;
    }
}
